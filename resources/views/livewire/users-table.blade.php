<div>
        <div class="row mb-3">
            <div class="col form-inline">
                Per Page: &nbsp;
                <select wire:model="perPage" class="form-control">
                    <option>5</option>
                    <option>10</option>
                    <option>15</option>
                    <option>25</option>
                </select>
            </div>
    
            <div class="col">
                <input wire:model="search" class="form-control" type="text" placeholder="Search User...">
            </div>
        </div>
    
        <div class="clearfix">
            <table class="table table-condensed table-hover table-striped">
                <thead>
                    <tr>
                        <th><a wire:click.prevent="sortBy('name')" role="button" href="#">
                            Name
                            @include('includes._sort-icon', ['field' => 'name'])
                        </a></th>
                        <th><a wire:click.prevent="sortBy('username')" role="button" href="#">
                            Username
                            @include('includes._sort-icon', ['field' => 'username'])
                        </a></th>
                        <th><a wire:click.prevent="sortBy('email')" role="button" href="#">
                            Email
                            @include('includes._sort-icon', ['field' => 'email'])
                        </a></th>
                        <th><a wire:click.prevent="sortBy('role')" role="button" href="#">
                            Role
                            @include('includes._sort-icon', ['field' => 'role'])
                        </a></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role_name }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}"><span><i class="fa fa-edit" title="update info"></i></span></a>
                                <a href="{{ route('users.changepassword', $user->id) }}"><span><i class="fa fa-key" title="change password"></i></span></a>
                                @if ($user->username != 'admin')
                                    <a href="{{ route('users.delete', $user->id) }}" 
                                        onclick="event.preventDefault();
                                            document.getElementById('delete-user-form-{{ $user->id }}').submit();"><span><i class="fa fa-trash-o" title="delete" style="color:red"></i></span></a>
        
                                    <form id="delete-user-form-{{ $user->id }}" method="POST" action="{{ route('users.delete', $user->id) }}">
                                        @csrf
                                        <input type="text" class="form-control" name="id" value="{{ $user->id }}" hidden />
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
        <div class="row">
            <div class="col">
                {{ $users->links() }}
            </div>
    
            <div class="col text-right text-muted">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} out of {{ $users->total() }} results
            </div>
        </div>
    </div>