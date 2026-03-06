<?php

namespace App\Http\Controllers;

use App\GoldPrice;
use App\InventoryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GoldPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->assertAdminRole();
        $inventoryStatuses = $this->getMatrixInventoryStatuses();
        $rateColumns = $this->getRateColumns();
        $selectedPriceDate = old('price_date', now()->toDateString());
        $todayBasePriceList = $this->getTodayBasePriceList();
        $matrixDefaults = $this->getMatrixDefaults($inventoryStatuses, $rateColumns, $selectedPriceDate);
        [$historyMatrices, $historyPaginator] = $this->getHistoryMatrices($inventoryStatuses, $rateColumns);

        return view('gold-prices.index', [
            'todayBasePriceList' => $todayBasePriceList,
            'inventoryStatuses' => $inventoryStatuses,
            'rateColumns' => $rateColumns,
            'selectedPriceDate' => $selectedPriceDate,
            'matrixDefaults' => $matrixDefaults,
            'historyMatrices' => $historyMatrices,
            'historyPaginator' => $historyPaginator,
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdminRole();

        $requiredColumns = ['gold_rate', 'inventory_status_id', 'base_price', 'service_fee'];
        $missingColumns = [];
        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('gold_prices', $column)) {
                $missingColumns[] = $column;
            }
        }
        if (count($missingColumns) > 0) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'schema' => __('Gold price table is missing columns: :columns. Please run migration first.', [
                        'columns' => implode(', ', $missingColumns),
                    ]),
                ]);
        }

        $request->validate([
            'price_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $matrix = $request->input('matrix', []);
        $inventoryStatuses = $this->getMatrixInventoryStatuses();
        $rateColumns = $this->getRateColumns();
        $entries = [];
        $errors = [];

        foreach ($inventoryStatuses as $inventoryStatus) {
            $statusMatrix = $matrix[$inventoryStatus->id] ?? [];

            foreach ($rateColumns as $rateColumn) {
                $rateMatrix = $statusMatrix[$rateColumn['key']] ?? [];
                $rawBasePrice = trim((string) ($rateMatrix['base_price'] ?? ''));
                $rawServiceFee = trim((string) ($rateMatrix['service_fee'] ?? ''));
                $hasBasePrice = $rawBasePrice !== '';
                $hasServiceFee = $rawServiceFee !== '';

                if (!$hasBasePrice && !$hasServiceFee) {
                    continue;
                }

                if (!$hasBasePrice || !$hasServiceFee) {
                    $errors[] = sprintf(
                        '%s (%s%%): base price and service fee must both be filled.',
                        $inventoryStatus->description,
                        $rateColumn['label']
                    );
                    continue;
                }

                $normalizedBasePrice = $this->normalizeLocalizedPrice($rawBasePrice);
                $normalizedServiceFee = $this->normalizeLocalizedPrice($rawServiceFee);
                if ($normalizedBasePrice === null || !is_numeric($normalizedBasePrice) || (float) $normalizedBasePrice < 0) {
                    $errors[] = sprintf('%s (%s%%): invalid base price format.', $inventoryStatus->description, $rateColumn['label']);
                    continue;
                }
                if ($normalizedServiceFee === null || !is_numeric($normalizedServiceFee) || (float) $normalizedServiceFee < 0) {
                    $errors[] = sprintf('%s (%s%%): invalid service fee format.', $inventoryStatus->description, $rateColumn['label']);
                    continue;
                }

                $entries[] = [
                    'inventory_status_id' => (int) $inventoryStatus->id,
                    'gold_rate' => round((float) $rateColumn['value'], 2),
                    'base_price' => round((float) $normalizedBasePrice, 2),
                    'service_fee' => round((float) $normalizedServiceFee, 2),
                ];
            }
        }

        if (count($errors) > 0) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'matrix' => implode(' ', $errors),
                ]);
        }

        if (count($entries) === 0) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'matrix' => __('Please fill at least one base price and service fee pair.'),
                ]);
        }

        DB::transaction(function () use ($entries, $request) {
            foreach ($entries as $entry) {
                $goldPrice = new GoldPrice();
                if (Schema::hasColumn('gold_prices', 'price_date')) {
                    $goldPrice->price_date = $request->get('price_date');
                }
                if (Schema::hasColumn('gold_prices', 'gold_rate')) {
                    $goldPrice->gold_rate = $entry['gold_rate'];
                }
                if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
                    $goldPrice->inventory_status_id = $entry['inventory_status_id'];
                }
                if (Schema::hasColumn('gold_prices', 'base_price')) {
                    $goldPrice->base_price = $entry['base_price'];
                }
                if (Schema::hasColumn('gold_prices', 'service_fee')) {
                    $goldPrice->service_fee = $entry['service_fee'];
                }
                if (Schema::hasColumn('gold_prices', 'min_price')) {
                    $goldPrice->min_price = $entry['base_price'];
                }
                if (Schema::hasColumn('gold_prices', 'max_price')) {
                    $goldPrice->max_price = $entry['base_price'];
                }
                if (Schema::hasColumn('gold_prices', 'notes')) {
                    $goldPrice->notes = $request->get('notes');
                }
                if (Schema::hasColumn('gold_prices', 'created_by_user_id')) {
                    $goldPrice->created_by_user_id = Auth::id();
                }
                if (Schema::hasColumn('gold_prices', 'created_by')) {
                    $goldPrice->created_by = Auth::user()->name;
                }
                $goldPrice->save();
            }
        });

        return redirect()->route('gold-prices.index')->with('success', __('Gold base price has been added.'));
    }

    private function normalizeLocalizedPrice($value)
    {
        $value = trim((string) $value);
        $value = str_ireplace('rp', '', $value);
        $value = preg_replace('/\s+/', '', $value);

        if ($value === '') {
            return null;
        }

        // Indonesian format: 20.342,25 -> 20342.25
        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            // Indonesian thousands without decimal: 100.000 -> 100000
            $value = str_replace('.', '', $value);
        }

        return $value;
    }

    private function getTodayBasePriceList()
    {
        $today = now()->toDateString();
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        $inventoryStatusMap = [];

        $goldPriceQuery = GoldPrice::query();
        if ($hasInventoryStatusColumn) {
            $goldPriceQuery = $goldPriceQuery->with('inventoryStatus');
            $inventoryStatusMap = InventoryStatus::withTrashed()
                ->pluck('description', 'id')
                ->toArray();
        }
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('price_date', '<=', $today)
                ->orderBy('price_date', 'desc');
        } else {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('created_at', '<=', $today)
                ->orderBy('created_at', 'desc');
        }
        $goldPrices = $goldPriceQuery
            ->orderBy('id', 'desc')
            ->get();

        $result = [];
        foreach ($goldPrices as $goldPrice) {
            $displayBasePrice = $goldPrice->base_price ?? $goldPrice->max_price ?? $goldPrice->min_price;
            if ($displayBasePrice === null) {
                continue;
            }

            $displayGoldRate = $hasGoldRateColumn ? $goldPrice->gold_rate : null;
            $displayInventoryStatus = null;
            if ($hasInventoryStatusColumn) {
                $displayInventoryStatus = optional($goldPrice->inventoryStatus)->description;
                if ($displayInventoryStatus === null && $goldPrice->inventory_status_id !== null) {
                    $displayInventoryStatus = $inventoryStatusMap[$goldPrice->inventory_status_id] ?? null;
                }
            }

            if ($hasGoldRateColumn && $displayGoldRate === null) {
                continue;
            }
            if ($hasInventoryStatusColumn && $displayInventoryStatus === null) {
                continue;
            }

            $result[] = [
                'base_price' => $displayBasePrice,
                'service_fee' => $goldPrice->service_fee ?? 0,
                'gold_rate' => $displayGoldRate,
                'inventory_status' => $displayInventoryStatus,
            ];
        }

        return $result;
    }

    private function assertAdminRole()
    {
        if (Auth::user()->authRole()->name !== 'admin') {
            abort(403);
        }
    }

    private function getRateColumns()
    {
        $defaultRates = [37.5, 42.0];
        $rateMap = [];

        foreach ($defaultRates as $defaultRate) {
            $this->registerRateValue($rateMap, $defaultRate);
        }

        if (Schema::hasColumn('gold_prices', 'gold_rate')) {
            $goldPriceRates = GoldPrice::query()
                ->whereNotNull('gold_rate')
                ->select('gold_rate')
                ->distinct()
                ->pluck('gold_rate')
                ->all();
            foreach ($goldPriceRates as $goldPriceRate) {
                $this->registerRateValue($rateMap, $goldPriceRate);
            }
        }

        if (Schema::hasTable('item') && Schema::hasColumn('item', 'item_gold_rate')) {
            $itemRates = DB::table('item')
                ->whereNotNull('item_gold_rate')
                ->select('item_gold_rate')
                ->distinct()
                ->pluck('item_gold_rate')
                ->all();
            foreach ($itemRates as $itemRate) {
                $this->registerRateValue($rateMap, $itemRate);
            }
        }

        $orderedRateMap = [];
        foreach ($defaultRates as $defaultRate) {
            $defaultRateKey = $this->toRateValueKey($defaultRate);
            if (isset($rateMap[$defaultRateKey])) {
                $orderedRateMap[$defaultRateKey] = $rateMap[$defaultRateKey];
            }
        }

        uksort($rateMap, function ($left, $right) {
            return ((float) $left) <=> ((float) $right);
        });
        foreach ($rateMap as $rateKey => $rateValue) {
            if (isset($orderedRateMap[$rateKey])) {
                continue;
            }
            $orderedRateMap[$rateKey] = $rateValue;
        }

        $rateColumns = [];
        foreach ($orderedRateMap as $rateKey => $rateValue) {
            $rateColumns[] = [
                'key' => $this->toRateColumnKey($rateValue),
                'label' => $this->toRateLabel($rateValue),
                'value' => (float) $rateValue,
                'value_key' => $rateKey,
            ];
        }

        return $rateColumns;
    }

    private function getMatrixInventoryStatuses()
    {
        return InventoryStatus::query()
            ->orderByRaw("CASE WHEN LOWER(description) = 'general' THEN 0 ELSE 1 END")
            ->orderBy('description', 'asc')
            ->get();
    }

    private function getMatrixDefaults($inventoryStatuses, $rateColumns, $selectedPriceDate)
    {
        $matrix = $this->buildEmptyMatrix($inventoryStatuses, $rateColumns);
        $hasPriceDateColumn = Schema::hasColumn('gold_prices', 'price_date');
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        if (!$hasGoldRateColumn || !$hasInventoryStatusColumn) {
            return $matrix;
        }

        foreach ($inventoryStatuses as $inventoryStatus) {
            foreach ($rateColumns as $rateColumn) {
                $goldPriceQuery = GoldPrice::query();
                if ($hasPriceDateColumn) {
                    $goldPriceQuery = $goldPriceQuery
                        ->whereDate('price_date', '=', $selectedPriceDate)
                        ->orderBy('price_date', 'desc');
                } else {
                    $goldPriceQuery = $goldPriceQuery
                        ->whereDate('created_at', '=', $selectedPriceDate)
                        ->orderBy('created_at', 'desc');
                }

                $goldPriceQuery = $goldPriceQuery->where('gold_rate', (float) $rateColumn['value']);
                $goldPriceQuery = $goldPriceQuery->where('inventory_status_id', (int) $inventoryStatus->id);

                $goldPrice = $goldPriceQuery->orderBy('id', 'desc')->first();
                if (!$goldPrice) {
                    continue;
                }

                $displayBasePrice = $goldPrice->base_price ?? $goldPrice->max_price ?? $goldPrice->min_price;
                $matrix[$inventoryStatus->id][$rateColumn['key']] = [
                    'base_price' => $displayBasePrice !== null ? (float) $displayBasePrice : null,
                    'service_fee' => $goldPrice->service_fee !== null ? (float) $goldPrice->service_fee : null,
                ];
            }
        }

        return $matrix;
    }

    private function getHistoryMatrices($inventoryStatuses, $rateColumns)
    {
        $hasPriceDateColumn = Schema::hasColumn('gold_prices', 'price_date');
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        if (!$hasGoldRateColumn || !$hasInventoryStatusColumn) {
            $emptyPaginator = GoldPrice::query()->whereRaw('1 = 0')->paginate(10, ['*'], 'history_page');
            return [[], $emptyPaginator];
        }

        $historyDateExpression = $hasPriceDateColumn ? 'price_date' : 'DATE(created_at)';
        $historyDatePaginator = GoldPrice::query()
            ->selectRaw($historyDateExpression . ' as history_date')
            ->whereNotNull($hasPriceDateColumn ? 'price_date' : 'created_at')
            ->groupBy(DB::raw($historyDateExpression))
            ->orderBy(DB::raw($historyDateExpression), 'desc')
            ->paginate(10, ['*'], 'history_page');

        $historyDates = collect($historyDatePaginator->items())
            ->pluck('history_date')
            ->filter()
            ->values()
            ->all();

        if (count($historyDates) === 0) {
            return [[], $historyDatePaginator];
        }

        $historyRowsQuery = GoldPrice::query();
        if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
            $historyRowsQuery = $historyRowsQuery->with('inventoryStatus');
        }
        if ($hasPriceDateColumn) {
            $historyRowsQuery = $historyRowsQuery->whereIn('price_date', $historyDates);
        } else {
            $historyRowsQuery = $historyRowsQuery->whereIn(DB::raw('DATE(created_at)'), $historyDates);
        }

        $historyRows = $historyRowsQuery
            ->orderBy($hasPriceDateColumn ? 'price_date' : 'created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $rateKeyMap = [];
        foreach ($rateColumns as $rateColumn) {
            $rateKeyMap[$rateColumn['value_key']] = $rateColumn['key'];
        }

        $historyMatrices = [];
        foreach ($historyRows as $historyRow) {
            $dateKey = $hasPriceDateColumn
                ? (string) $historyRow->price_date
                : optional($historyRow->created_at)->toDateString();
            if ($dateKey === null) {
                continue;
            }

            if (!isset($historyMatrices[$dateKey])) {
                $historyMatrices[$dateKey] = [
                    'price_date' => $dateKey,
                    'matrix' => $this->buildEmptyMatrix($inventoryStatuses, $rateColumns),
                    'notes' => $historyRow->notes,
                    'created_by' => $historyRow->created_by,
                    'created_at' => $historyRow->created_at,
                ];
            }

            $statusId = $historyRow->inventory_status_id;
            $rateKey = $rateKeyMap[$this->toRateValueKey($historyRow->gold_rate)] ?? null;
            if ($statusId === null || $rateKey === null || !isset($historyMatrices[$dateKey]['matrix'][$statusId])) {
                continue;
            }

            $existingCell = $historyMatrices[$dateKey]['matrix'][$statusId][$rateKey] ?? null;
            if ($existingCell && ($existingCell['base_price'] !== null || $existingCell['service_fee'] !== null)) {
                // Rows are already ordered newest-first; keep first value per status+rate.
                continue;
            }

            $displayBasePrice = $historyRow->base_price ?? $historyRow->max_price ?? $historyRow->min_price;
            $historyMatrices[$dateKey]['matrix'][$statusId][$rateKey] = [
                'base_price' => $displayBasePrice !== null ? (float) $displayBasePrice : null,
                'service_fee' => $historyRow->service_fee !== null ? (float) $historyRow->service_fee : null,
            ];
        }

        $orderedHistoryMatrices = [];
        foreach ($historyDates as $historyDate) {
            if (isset($historyMatrices[$historyDate])) {
                $orderedHistoryMatrices[] = $historyMatrices[$historyDate];
            }
        }

        return [$orderedHistoryMatrices, $historyDatePaginator];
    }

    private function buildEmptyMatrix($inventoryStatuses, $rateColumns)
    {
        $matrix = [];
        foreach ($inventoryStatuses as $inventoryStatus) {
            $matrix[(int) $inventoryStatus->id] = [];
            foreach ($rateColumns as $rateColumn) {
                $matrix[(int) $inventoryStatus->id][$rateColumn['key']] = [
                    'base_price' => null,
                    'service_fee' => null,
                ];
            }
        }

        return $matrix;
    }

    private function toRateValueKey($value)
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function registerRateValue(&$rateMap, $value)
    {
        if ($value === null || !is_numeric($value)) {
            return;
        }

        $normalizedValue = round((float) $value, 2);
        if ($normalizedValue <= 0) {
            return;
        }

        $rateMap[$this->toRateValueKey($normalizedValue)] = $normalizedValue;
    }

    private function toRateColumnKey($value)
    {
        return 'rate_' . str_replace('.', '_', $this->toRateValueKey($value));
    }

    private function toRateLabel($value)
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
