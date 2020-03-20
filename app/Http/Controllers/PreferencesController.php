<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;

class PreferencesController extends Controller
{
    public function store() {
        return view('preferences.store', [
            'stores' => Store::sortable()->paginate(10),
        ]);
    }

    public function category() {
        return view('preferences.category', [
            
        ]);
    }

    public function allocation() {
        return view('preferences.allocation', [
            
        ]);
    }

    public function itemStatus() {
        return view('preferences.itemstatus', [
            
        ]);
    }

    public function buybackStatus() {
        return view('preferences.buybackstatus', [
            
        ]);
    }

    public function salesStatus() {
        return view('preferences.salesstatus', [
            
        ]);
    }
}
