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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Allocation...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Allocation Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Allocation Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allocations as $allocation)
                    <tr>
                        <td>{{ $allocation->code }}</td>
                        <td>{{ $allocation->description }}</td>
                        <td>
                        	<a href="{{ route('allocations.edit', $allocation->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('allocations.delete', $allocation->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-allocation-form-{{ $allocation->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-allocation-form-{{ $allocation->id }}" method="POST" action="{{ route('allocations.delete', $allocation->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $allocation->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $allocations->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $allocations->firstItem() }} to {{ $allocations->lastItem() }} out of {{ $allocations->total() }} results
        </div>
    </div>
</div>