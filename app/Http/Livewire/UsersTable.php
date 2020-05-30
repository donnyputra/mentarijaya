<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class UsersTable extends Component
{
    use WithPagination;	

    const INITIAL_SORT_FIELD = 'username';

    public $perPage = 10;
    public $sortField = self::INITIAL_SORT_FIELD;
    public $sortAsc = true;
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
        $users = DB::table('users')
                    ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                    ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                    ->select(
                        'users.*',
                        'roles.name as role_name'
                    )

                    // apply search textbox
                    ->where(function($querySearchInput){
                        return $querySearchInput->when($this->search, function($querySearchResult, $searchKeyword) {
                            return $querySearchResult->where('users.name', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('users.username', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('users.email', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('roles.name', 'like', '%'.$searchKeyword.'%');
                        });
                    })
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->perPage);

        return view('livewire.users-table', [
            'users' => $users,
        ]);
    }

}
