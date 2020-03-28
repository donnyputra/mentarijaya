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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Sales Status...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Sales Status Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Sales Status Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesstatuses as $salesstatus)
                    <tr>
                        <td>{{ $salesstatus->code }}</td>
                        <td>{{ $salesstatus->description }}</td>
                        <td>
                        	<a href="{{ route('salesstatuses.edit', $salesstatus->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('salesstatuses.delete', $salesstatus->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-salesstatus-form-{{ $salesstatus->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-salesstatus-form-{{ $salesstatus->id }}" method="POST" action="{{ route('salesstatuses.delete', $salesstatus->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $salesstatus->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $salesstatuses->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $salesstatuses->firstItem() }} to {{ $salesstatuses->lastItem() }} out of {{ $salesstatuses->total() }} results
        </div>
    </div>
</div>