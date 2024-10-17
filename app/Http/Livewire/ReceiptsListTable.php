<?php

namespace App\Http\Livewire;

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
        return view('livewire.receipts-list-table', [
            'receipts' => \App\Receipts::search($this->search)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }
}
