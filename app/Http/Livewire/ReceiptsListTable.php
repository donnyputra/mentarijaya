<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReceiptsListTable extends Component
{
    use WithPagination;	

    const INITIAL_SORT_FIELD = 'id';

    public $perPage = 10;
    public $sortField = self::INITIAL_SORT_FIELD;
    public $sortAsc = false;
    public $search = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $query = \App\Receipts::search($this->search)
            ->with('details.item');

        if (Auth::user()->authRole()->name !== 'admin') {
            $query->whereHas('details')
                ->whereDoesntHave('details.item', function ($builder) {
                    $builder->whereNull('sales_approved_at');
                });
        }

        return view('livewire.receipts-list-table', [
            'receipts' => $query
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }
}
