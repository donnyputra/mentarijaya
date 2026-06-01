<?php

namespace App\Http\Controllers;

use App\GoldPrice;
use App\InventoryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $goldPrices = GoldPrice::query();
        if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
            $goldPrices = $goldPrices->with('inventoryStatus');
        }
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPrices = $goldPrices->orderBy('price_date', 'desc');
        } else {
            $goldPrices = $goldPrices->orderBy('created_at', 'desc');
        }
        if (Schema::hasColumn('gold_prices', 'gold_rate')) {
            $goldPrices = $goldPrices->orderBy('gold_rate', 'asc');
        }
        if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
            $goldPrices = $goldPrices->orderBy('inventory_status_id', 'asc');
        }

        $goldPrices = $goldPrices
            ->orderBy('id', 'desc')
            ->paginate(20);

        $todayBasePriceList = $this->getTodayBasePriceList();

        return view('gold-prices.index', [
            'goldPrices' => $goldPrices,
            'todayBasePriceList' => $todayBasePriceList,
            'inventoryStatuses' => InventoryStatus::orderBy('description', 'asc')->get(),
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
            'gold_rate' => 'required|numeric|min:0',
            'inventory_status_id' => 'required|integer|exists:inventory_status,id',
            'base_price' => 'required',
            'service_fee' => 'required',
            'notes' => 'nullable|string|max:1000',
        ]);

        $normalizedBasePrice = $this->normalizeLocalizedPrice($request->get('base_price'));
        if ($normalizedBasePrice === null || !is_numeric($normalizedBasePrice) || (float) $normalizedBasePrice < 0) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'base_price' => __('Base price format is invalid. Use format like 20.342,25'),
                ]);
        }

        $normalizedServiceFee = $this->normalizeLocalizedPrice($request->get('service_fee'));
        if ($normalizedServiceFee === null || !is_numeric($normalizedServiceFee) || (float) $normalizedServiceFee < 0) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'service_fee' => __('Service fee format is invalid. Use format like 2.500,00'),
                ]);
        }

        $basePrice = round((float) $normalizedBasePrice, 2);
        $serviceFee = round((float) $normalizedServiceFee, 2);
        $goldRate = round((float) $request->get('gold_rate'), 2);
        $inventoryStatusId = (int) $request->get('inventory_status_id');

        $goldPrice = new GoldPrice();
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPrice->price_date = $request->get('price_date');
        }
        if (Schema::hasColumn('gold_prices', 'gold_rate')) {
            $goldPrice->gold_rate = $goldRate;
        }
        if (Schema::hasColumn('gold_prices', 'inventory_status_id')) {
            $goldPrice->inventory_status_id = $inventoryStatusId;
        }
        if (Schema::hasColumn('gold_prices', 'base_price')) {
            $goldPrice->base_price = $basePrice;
        }
        if (Schema::hasColumn('gold_prices', 'service_fee')) {
            $goldPrice->service_fee = $serviceFee;
        }
        if (Schema::hasColumn('gold_prices', 'min_price')) {
            $goldPrice->min_price = $basePrice;
        }
        if (Schema::hasColumn('gold_prices', 'max_price')) {
            $goldPrice->max_price = $basePrice;
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
}
