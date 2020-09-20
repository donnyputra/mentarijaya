@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Item Book") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Item Book") }}</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-right">
                                <a class="btn btn-primary mb-3" href="{{ route('items.create') }}"
                                    role="button"><span><i class="nav-icon fas fa-plus"></i></span></a>
                                <a class="btn btn-secondary mb-3" href="{{ route('items.bulkupload') }}"
                                    role="button">{{ __("Bulk Upload") }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <form action="{{ route('items.massaction') }}" method="post">
                                                    <div class="form-row align-items-center">
                                                        <div class="col-auto">
                                                            <label for="mass_action">Mass Action</label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <select class="form-control" name="mass_action" id="mass_action">
                                                                <option value="approveitems">Approve Items</option>
                                                                <option value="approvesales">Approve Sales</option>
                                                                <!-- <option value="rejectitems">Reject Items</option>
                                                                <option value="rejectsales">Reject Sales</option> -->
                                                            </select>
                                                        </div>
                                                        <div class="col-auto">
                                                            @csrf
                                                            <input type="text" id="mass_action_data" name="mass_action_data" hidden />
                                                        </div>
                                                        <div class="col-auto">
                                                            <button id="btnMassAction" type="submit"
                                                                class="btn btn-primary">{{ __("Submit") }}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-secondary float-right" type="button"
                                                    data-toggle="modal" data-target="#advanceFilter"
                                                    aria-expanded="false" aria-controls="advanceFilter">Search &
                                                    Filter</button>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div id="advanceFilter" class="modal fade" tabindex="-1" role="dialog"
                                                    aria-labelledby="Advance Filter" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                                    Search & Filter</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Search by Name
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-8">
                                                                        <input class="form-control" type="text"
                                                                            id="txtSearch"
                                                                            placeholder="Search by Name...">
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Show Items
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4">
                                                                        <select class="form-control" id="itemperpage">
                                                                            <option value="10">10</option>
                                                                            <option value="20">20</option>
                                                                            <option value="50">50</option>
                                                                            <option value="100">100</option>
                                                                            <option value="200">200</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Filter by Attribute
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4">
                                                                        <select class="form-control" id="filterStore">
                                                                            <option value="">- All Stores -</option>
                                                                            @foreach ($stores as $store)
                                                                            <option value="{{ $store->id }}">
                                                                                {{ $store->name }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <select class="form-control"
                                                                            id="filterCategory">
                                                                            <option value="">- All Categories -
                                                                            </option>
                                                                            @foreach ($categories as $category)
                                                                            <option value="{{ $category->id }}">
                                                                                {{ $category->description }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <select class="form-control"
                                                                            id="filterAllocation">
                                                                            <option value="">- All Allocations -
                                                                            </option>
                                                                            @foreach ($allocations as $allocation)
                                                                            <option value="{{ $allocation->id }}">
                                                                                {{ $allocation->description }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4">
                                                                        <select class="form-control"
                                                                            id="filterItemStatus">
                                                                            <option value="">- All Item Statuses -
                                                                            </option>
                                                                            @foreach ($itemstatuses as $itemstatus)
                                                                            <option value="{{ $itemstatus->id }}">
                                                                                {{ $itemstatus->description }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <select class="form-control"
                                                                            id="filterInventoryStatus">
                                                                            <option value="">- All Inventory
                                                                                Statuses -
                                                                            </option>
                                                                            @foreach ($inventorystatuses as
                                                                            $inventorystatus)
                                                                            <option value="{{ $inventorystatus->id }}">
                                                                                {{ $inventorystatus->description }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <select class="form-control"
                                                                            id="filterSalesStatus">
                                                                            <option value="">- All Sales Statuses -
                                                                            </option>
                                                                            @foreach ($salesstatuses as
                                                                            $salesstatus)
                                                                            <option value="{{ $salesstatus->id }}">
                                                                                {{ $salesstatus->description }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Filter by Date
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-4">
                                                                        <input type="text"
                                                                            class="form-control dateselect"
                                                                            id="dpStartDate" name="dpStartDate"
                                                                            placeholder="Start Date"
                                                                            value="{{ Request::get('startdate') }}" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <input type="text"
                                                                            class="form-control dateselect"
                                                                            id="dpEndDate" name="dpEndDate"
                                                                            placeholder="End Date"
                                                                            value="{{ Request::get('enddate') }}" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <button class="btn btn-secondary" id="btnSetToday">Set Today</button>
                                                                        <button class="btn btn-secondary" id="btnDpClear">Clear</button>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- /.modal-body -->
                                                            <div class="modal-footer">
                                                                <button id="btnApplyAdvanceFilter"
                                                                    class="btn btn-primary">
                                                                    Apply
                                                                </button>
                                                                <button id="btnClearAllFilter" class="btn btn-warning">
                                                                    Clear All
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- /.advanceFilter Modal -->

                                            </div>
                                        </div>

                                        <div class="clearfix">
                                            <table
                                                class="table table-responsive table-hover table-striped table-head-fixed text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <input type="checkbox" class="select-all checkbox"
                                                                name="select-all" />
                                                        </th>
                                                        <th>Action</th>
                                                        <th>Store</th>
                                                        <th>Item No</th>
                                                        <th>Item Name</th>
                                                        <th>Category</th>
                                                        <th>Item Weight</th>
                                                        <th>Sales Price</th>
                                                        <th>Sales At</th>
                                                        <th>Sales Status</th>
                                                        <th>Allocation</th>
                                                        <th>Item Status</th>
                                                        <th>Inventory Status</th>
                                                        <th>Gold Rate</th>
                                                        <th>Created By</th>
                                                        <th>Sales By</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($items as $item)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" class="select-item checkbox"
                                                                name="select-item" value="{{ $item->id }}" />
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('items.edit', $item->id) }}"><span><i
                                                                        class="fa fa-edit"></i></span></a>
                                                            <a href="{{ route('items.delete', $item->id) }}" onclick="event.preventDefault();
                                    var r = confirm('Are you sure you want to delete this?');
                                    if(r == true) {
                                        document.getElementById('delete-item-form-{{ $item->id }}').submit();
                                    }"><span><i class="fa fa-trash" style="color:red"></i></span></a>

                                                            <form id="delete-item-form-{{ $item->id }}" method="POST"
                                                                action="{{ route('items.delete', $item->id) }}">
                                                                @csrf
                                                                <input type="text" class="form-control" name="id"
                                                                    value="{{ $item->id }}" hidden />
                                                            </form>
                                                        </td>
                                                        <td>{{ $item->store_name }}</td>
                                                        <td>{{ $item->item_no }}</td>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->category_description }}</td>
                                                        <td>{{ ($item->item_weight) . " gr" }}</td>
                                                        <td>{{ $item->sales_price == null ? "-" : ("Rp " . number_format($item->sales_price, 2, ',', '.')) }}
                                                        </td>
                                                        <td>{{ $item->sales_at == null ? "-" : Carbon\Carbon::parse($item->sales_at)->format('m/d/Y') }}
                                                        </td>
                                                        <td>{{ $item->sales_status_description == null ? "-" : $item->sales_status_description }}
                                                        </td>

                                                        <td>{{ $item->allocation_description }}</td>
                                                        <td>{{ $item->item_status_description}}</td>
                                                        <td>{{ $item->inventory_status_description}}</td>
                                                        <td>{{ ($item->item_gold_rate) . "%" }}</td>
                                                        <td>{{ $item->created_by == null ? "-" : $item->created_by_name }}
                                                        </td>
                                                        <td>{{ $item->sales_by == null ? "-" : $item->sales_by_name }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($item->created_at)->format('m/d/Y') }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($item->updated_at)->format('m/d/Y') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div> <!-- ./Table Grid -->

                                        <div class="row">
                                            <div class="col">
                                                {{ $items->appends(request()->input())->links() }}
                                            </div>

                                            <div class="col text-right text-muted">
                                                Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} out of
                                                {{ $items->total() }} results
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script type="text/javascript">
$(function() {
    //Init Modal Advance Filter
    $('#filterStore').val("{{ Request::get('store') }}");
    $('#filterCategory').val("{{ Request::get('category') }}");
    $('#filterAllocation').val("{{ Request::get('allocation') }}");
    $('#filterItemStatus').val("{{ Request::get('itemstatus') }}");
    $('#filterInventoryStatus').val("{{ Request::get('inventorystatus') }}");
    $('#filterSalesStatus').val("{{ Request::get('salesstatus') }}");
    $('#txtSearch').val("{{ Request::get('search') }}")

    if ("{{ Request::get('itemperpage') }}" == '')
        $('#itemperpage').val("10"); //set minimum items per page
    else
        $('#itemperpage').val("{{ Request::get('itemperpage') }}");

    //initialize start date
    $('#dpStartDate').datepicker({
            format: 'dd-mm-yyyy'
        })
        // .datepicker('setDate', "{{ Request::get('startdate') ? Request::get('startdate') : date('d-m-Y') }}")
        .datepicker('setDate', "{{ Request::get('startdate') }}")
        .on('changeDate', function(e) { // TODO:validate start date
            //start date can't be larger than end date
            console.log(this.value);
        });

    //initialize end date
    $('#dpEndDate').datepicker({
            format: 'dd-mm-yyyy'
        })
        // .datepicker('setDate', "{{ Request::get('enddate') ? Request::get('enddate') : date('d-m-Y') }}")
        .datepicker('setDate', "{{ Request::get('enddate') }}")
        .on('changeDate', function(e) { // TODO:validate end date
            console.log(this.value);
        });

    $('#btnSetToday').on('click', function(e) {
        var todayDate = "{{ date('d-m-Y') }}";
        $('#dpStartDate').val(todayDate);
        $('#dpEndDate').val(todayDate);
    });

    $('#btnDpClear').on('click', function(e) {
        $('#dpStartDate').val("");
        $('#dpEndDate').val("");
    });

    $('#btnApplyAdvanceFilter').on('click', function(e) {
        var startDate = $('#dpStartDate').val();
        var endDate = $('#dpEndDate').val();
        var filterStore = $('#filterStore').val();
        var filterCategory = $('#filterCategory').val();
        var filterAllocation = $('#filterAllocation').val();
        var filterItemStatus = $('#filterItemStatus').val();
        var filterInventoryStatus = $('#filterInventoryStatus').val();
        var filterSalesStatus = $('#filterSalesStatus').val();
        var itemPerPage = $('#itemperpage').val();
        var search = $("#txtSearch").val();

        window.location = "{{ route('items.index') }}" +
            "?startdate=" + startDate +
            "&enddate=" + endDate +
            "&store=" + filterStore +
            "&category=" + filterCategory +
            "&allocation=" + filterAllocation +
            "&itemstatus=" + filterItemStatus +
            "&inventorystatus=" + filterInventoryStatus +
            "&salesstatus=" + filterSalesStatus +
            "&itemperpage=" + itemPerPage +
            "&search=" + search;
    });

    $("#btnClearAllFilter").on('click', function(e) {
        var arr = window.location.href.split('?');
        window.location = arr[0];
    });


    //button select all or cancel
    $("#select-all").click(function() {
        var all = $("input.select-all")[0];
        all.checked = !all.checked
        var checked = all.checked;
        $("input.select-item").each(function(index, item) {
            item.checked = checked;
        });
    });
    //column checkbox select all or cancel
    $("input.select-all").click(function() {
        var checked = this.checked;
        $("input.select-item").each(function(index, item) {
            item.checked = checked;
        });
    });
    //check selected items
    $("input.select-item").click(function() {
        var checked = this.checked;
        var all = $("input.select-all")[0];
        var total = $("input.select-item").length;
        var len = $("input.select-item:checked:checked").length;
        all.checked = len === total;
    });

    $("#btnMassAction").click(function() {
        var actionName = $("#mass_action").val();
        var ids = [];
        $('input[name="select-item"]:checked').each(function (index, item) {
            ids.push(item.value);
        });

        $("#mass_action_data").val(ids);
    });
});
</script>
@endsection