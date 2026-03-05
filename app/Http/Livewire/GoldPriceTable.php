<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class GoldPriceTable extends Component
{
	use WithPagination;	

    const INITIAL_SORT_FIELD = 'created_at';

    public $perPage = 10;
    public $sortField = self::INITIAL_SORT_FIELD;
    public $sortAsc = false;
    public $search = '';

    public function render()
    {
        return view('livewire.gold-price-table', [
            'goldpricehistories' => \App\GoldPrice::orderBy($this->sortField, 'desc')
                ->paginate($this->perPage),
        ]);
    }
}
