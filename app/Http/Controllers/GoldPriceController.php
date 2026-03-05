<?php

namespace App\Http\Controllers;

use App\GoldPrice;
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
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPrices = $goldPrices->orderBy('price_date', 'desc');
        } else {
            $goldPrices = $goldPrices->orderBy('created_at', 'desc');
        }

        $goldPrices = $goldPrices
            ->orderBy('id', 'desc')
            ->paginate(20);

        $todayBasePrice = $this->getTodayBasePrice();

        return view('gold-prices.index', [
            'goldPrices' => $goldPrices,
            'todayBasePrice' => $todayBasePrice,
        ]);
    }

    public function store(Request $request)
    {
        $this->assertAdminRole();

        $request->validate([
            'price_date' => 'required|date',
            'base_price' => 'required',
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

        $basePrice = round((float) $normalizedBasePrice, 2);

        $goldPrice = new GoldPrice();
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPrice->price_date = $request->get('price_date');
        }
        if (Schema::hasColumn('gold_prices', 'base_price')) {
            $goldPrice->base_price = $basePrice;
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

    private function getTodayBasePrice()
    {
        $today = now()->toDateString();

        $goldPriceQuery = GoldPrice::query();
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('price_date', '<=', $today)
                ->orderBy('price_date', 'desc');
        } else {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('created_at', '<=', $today)
                ->orderBy('created_at', 'desc');
        }

        $goldPrice = $goldPriceQuery->orderBy('id', 'desc')->first();

        if (!$goldPrice) {
            return null;
        }

        if (Schema::hasColumn('gold_prices', 'base_price') && $goldPrice->base_price !== null) {
            return $goldPrice->base_price;
        }

        if (Schema::hasColumn('gold_prices', 'max_price') && $goldPrice->max_price !== null) {
            return $goldPrice->max_price;
        }

        if (Schema::hasColumn('gold_prices', 'min_price') && $goldPrice->min_price !== null) {
            return $goldPrice->min_price;
        }

        return null;
    }

    private function assertAdminRole()
    {
        if (Auth::user()->authRole()->name !== 'admin') {
            abort(403);
        }
    }
}
