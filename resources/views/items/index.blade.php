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
                                <!-- <a class="btn btn-secondary mb-3" href="{{ route('items.bulkupload') }}"
                                    role="button">{{ __("Bulk Upload") }}</a> -->
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
                                                        <div class="col-auto mt-2">
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
                                                                        Search by Item No
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-8">
                                                                        <input class="form-control" type="text"
                                                                            id="txtSearchItemNo"
                                                                            placeholder="Search by Item No...">
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Search by Item Name
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-8">
                                                                        <input class="form-control" type="text"
                                                                            id="txtSearch"
                                                                            placeholder="Search by Item Name...">
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
                                                                        Filter by Item Entry Date
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-lg-6 col-sm-12">
                                                                        <input type="text"
                                                                            class="form-control dateselect"
                                                                            id="dpRangeDate" name="dpRangeDate"
                                                                            placeholder="Range Date"
                                                                            value="{{ Request::get('rangedate') }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-12">
                                                                        Filter by Sales Entry Date
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-lg-6 col-sm-12">
                                                                        <input type="text"
                                                                            class="form-control dateselect"
                                                                            id="dpSalesRangeDate" name="dpSalesRangeDate"
                                                                            placeholder="Sales Date"
                                                                            value="{{ Request::get('salesrangedate') }}" />
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
                                                                <button class="btn btn-secondary" data-toggle="modal" data-target="#printModal" aria-expanded="false" aria-controls="printModal">Print</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- /.advanceFilter Modal -->

                                                <div id="printModal" class="modal fade" tabindex="-1" role="dialog"
                                                    aria-labelledby="Print Modal" aria-hidden="true">
                                                    <div class="modal-dialog modal-md modal-dialog-centered"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="printModalTitle">
                                                                    Print</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=0 checked> Category
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=1 checked> Item No 
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=2 checked> Item Name
                                                                    </label>
                                                                </div>  
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=3 checked> Item Weight
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=4 checked> Sales Price
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=5 checked> Sales At
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=6 checked> Gold Rate
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=7 checked> Inventory Status
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=8 checked> Item Status
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=9 checked> Sales Status
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=10 checked> Created By
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=11 checked> Sales By
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=12 checked> Created At
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=13 checked> Updated At
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=14 checked> Allocation
                                                                    </label>
                                                                </div>
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="printed[]" value=15 checked> Store
                                                                    </label>
                                                                </div>
                                                                
                                                            </div> <!-- /.modal-body -->
                                                            <div class="modal-footer">
                                                                <button id="btnPrint"
                                                                    class="btn btn-primary">
                                                                    Print
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
                                                        <th>Category</th>
                                                        <th>Item No</th>
                                                        <th>Item Name</th>
                                                        <th>Item Weight</th>
                                                        <th>Sales Price</th>
                                                        <th>Sales At</th>
                                                        <th>Gold Rate</th>
                                                        <th>Inventory Status</th>
                                                        <th>Item Status</th>
                                                        <th>Sales Status</th>
                                                        <th>Created By</th>
                                                        <th>Sales By</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                        <th>Allocation</th>
                                                        <th>Store</th>
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
                                                        <td>{{ $item->category_description }}</td>
                                                        <td>{{ $item->item_no }}</td>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ StringHelper::formatDecimalDisplay($item->item_weight) . " gr" }}</td>
                                                        <td>{{ $item->sales_price == null ? "-" : ("Rp " . StringHelper::formatDecimalDisplay($item->sales_price)) }}
                                                        </td>
                                                        <td>{{ $item->sales_at == null ? "-" : Carbon\Carbon::parse($item->sales_at)->format('d-M-Y') }}
                                                        </td>
                                                        <td>{{ StringHelper::formatDecimalDisplay($item->item_gold_rate) . "%" }}</td>
                                                        <td>{{ $item->inventory_status_description}}</td>
                                                        <td>{{ $item->item_status_description}}</td>
                                                        <td>{{ $item->sales_status_description == null ? "-" : $item->sales_status_description }}
                                                        </td>
                                                        <td>{{ $item->created_by == null ? "-" : $item->created_by_name }}
                                                        </td>
                                                        <td>{{ $item->sales_by == null ? "-" : $item->sales_by_name }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($item->created_at)->format('d-M-Y') }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($item->updated_at)->format('d-M-Y') }}
                                                        </td>
                                                        <td>{{ $item->allocation_description }}</td>
                                                        <td>{{ $item->store_name }}</td>
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
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
    $('#txtSearchItemNo').val("{{ Request::get('searchitemno') }}")

    if ("{{ Request::get('itemperpage') }}" == '')
        $('#itemperpage').val("10"); //set minimum items per page
    else
        $('#itemperpage').val("{{ Request::get('itemperpage') }}");

    $('#dpSalesRangeDate').daterangepicker({
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        drops: 'up',
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-MMM-YYYY',
        },
    });

    $('#dpRangeDate').daterangepicker({
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        drops: 'up',
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-MMM-YYYY',
        },
    });

    $('#dpSalesRangeDate').val('');
    $('#dpRangeDate').val('');

    $('#dpSalesRangeDate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#dpRangeDate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#btnApplyAdvanceFilter').on('click', function(e) {
        var rangeDate = $('#dpRangeDate').val();
        var salesRangeDate = $('#dpSalesRangeDate').val();
        var filterStore = $('#filterStore').val();
        var filterCategory = $('#filterCategory').val();
        var filterAllocation = $('#filterAllocation').val();
        var filterItemStatus = $('#filterItemStatus').val();
        var filterInventoryStatus = $('#filterInventoryStatus').val();
        var filterSalesStatus = $('#filterSalesStatus').val();
        var itemPerPage = $('#itemperpage').val();
        var search = $("#txtSearch").val();
        var searchItemNo = $("#txtSearchItemNo").val();

        window.location = "{{ route('items.index') }}" +
            "?rangedate=" + rangeDate +
            "&rangesalesdate=" + salesRangeDate +
            "&store=" + filterStore +
            "&category=" + filterCategory +
            "&allocation=" + filterAllocation +
            "&itemstatus=" + filterItemStatus +
            "&inventorystatus=" + filterInventoryStatus +
            "&salesstatus=" + filterSalesStatus +
            "&itemperpage=" + itemPerPage +
            "&search=" + search +
            "&searchitemno=" + searchItemNo;
    });

    $('#btnPrint').on('click', function(e) {
        var rangeDate = $('#dpRangeDate').val();
        var salesRangeDate = $('#dpSalesRangeDate').val();
        var filterStore = $('#filterStore').val();
        var filterCategory = $('#filterCategory').val();
        var filterAllocation = $('#filterAllocation').val();
        var filterItemStatus = $('#filterItemStatus').val();
        var filterInventoryStatus = $('#filterInventoryStatus').val();
        var filterSalesStatus = $('#filterSalesStatus').val();
        var itemPerPage = $('#itemperpage').val();
        var search = $("#txtSearch").val();
        var searchItemNo = $("#txtSearchItemNo").val();
        var printed = $.map($(':checkbox[name=printed\\[\\]]:checked'), function(n, i){
            return n.value;
        }).join(',');

        window.location = "{{ route('pdf.items') }}" +
            "?rangedate=" + rangeDate +
            "&rangesalesdate=" + salesRangeDate +
            "&store=" + filterStore +
            "&category=" + filterCategory +
            "&allocation=" + filterAllocation +
            "&itemstatus=" + filterItemStatus +
            "&inventorystatus=" + filterInventoryStatus +
            "&salesstatus=" + filterSalesStatus +
            "&itemperpage=" + itemPerPage +
            "&search=" + search +
            "&printed=" + printed +
            "&searchitemno=" + searchItemNo;
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