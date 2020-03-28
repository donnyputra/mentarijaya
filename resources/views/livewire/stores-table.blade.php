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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Store...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('name')" role="button" href="#">
                        Name
                        @include('includes._sort-icon', ['field' => 'name'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('phone_no')" role="button" href="#">
                        Phone No
                        @include('includes._sort-icon', ['field' => 'phone_no'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('address')" role="button" href="#">
                        Address
                        @include('includes._sort-icon', ['field' => 'address'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stores as $store)
                    <tr>
                        <td>{{ $store->code }}</td>
                        <td>{{ $store->name }}</td>
                        <td>{{ $store->phone_no }}</td>
                        <td>{{ $store->address }}</td>
                        <td>
                        	<a href="{{ route('stores.edit', $store->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('stores.delete', $store->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-store-form-{{ $store->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-store-form-{{ $store->id }}" method="POST" action="{{ route('stores.delete', $store->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $store->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $stores->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $stores->firstItem() }} to {{ $stores->lastItem() }} out of {{ $stores->total() }} results
        </div>
    </div>
</div>