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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Category...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Category Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Category Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->code }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                        	<a href="{{ route('categories.edit', $category->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('categories.delete', $category->id) }}" 
                                onclick="event.preventDefault();
                                    var r = confirm('Are you sure you want to delete this?');
                                    if(r == true) {
                                        document.getElementById('delete-category-form-{{ $category->id }}').submit();
                                    }"><span><i class="fa fa-trash" style="color:red"></i></span></a>

                            <form id="delete-category-form-{{ $category->id }}" method="POST" action="{{ route('categories.delete', $category->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $category->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $categories->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} out of {{ $categories->total() }} results
        </div>
    </div>
</div>