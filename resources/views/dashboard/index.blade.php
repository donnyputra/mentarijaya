@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Dashboard") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Dashboard") }}</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <div class="card collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">STOK</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                        <table class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Code</th>
                                                    <th class="text-center">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($itemsCount as $item)
                                                <tr>
                                                    <td class="text-center">{{$item->category_code}}</td>
                                                    <td class="text-center">{{$item->item_count }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table> <!-- ./Table Grid -->

                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">IN STOCK ITEM BY GOLD RATE</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive">
                                    <table
                                        class="table table-hover table-striped text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Gold Rate</th>                                                
                                                <th>A</th>
                                                <th>CK</th>
                                                <th>C</th>
                                                <th>GL</th>
                                                <th>K</th>
                                                <th>L</th>
                                                <th>PT</th>
                                                <th>W</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($totalWeightSummaryCollection as $row)
                                                <tr>
                                                    <td><strong>{{ StringHelper::formatDecimalDisplay($row->item_gold_rate) . '%' }}</strong></td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->A) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->CK) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->C) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->GL) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->K) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->L) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->PT) . ' gr' }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->W) . ' gr' }}</td>
                                                    <td class="table-primary"><strong>{{ StringHelper::formatDecimalDisplay($row->TOTAL) . ' gr' }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div> <!-- /.card -->

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">DAILY SALES SUMMARY</h3>

                                        <div class="card-tools">
                                            <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button> -->
                                            <button class="btn btn-tool" type="button"
                                                    data-toggle="modal" data-target="#advanceFilter"
                                                    aria-expanded="false" aria-controls="advanceFilter">
                                                    <i class="fas fa-filter"></i></button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <table id="summary" class="table table-hover table-striped text-nowrap" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Gold Rate</th>
                                                    <th>Total Weight</th>
                                                    <th>Total Sales</th>
                                                    <th>Average</th>
                                                    <th>Sold Items</th>
                                                </tr>
                                            </thead>
                                        </table> <!-- ./Table Grid -->

                                    </div>
                                </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#summary').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "{{ route('summary.datatables') }}",
            columns: [
                {data: 'sales_date', name: 'sales_date'},
                {data: 'gold_rate', name: 'gold_rate'},
                {data: 'weight', name: 'weight'},
                {data: 'sales', name: 'sales'},
                {data: 'avg', name: 'avg'},
                {data: 'item_count', name: 'item_count', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            columnDefs: [
                { type: 'natural', targets: [2,3,4] },
                { render: DataTable.render.datetime('DD-MMM-YYYY'), targets: 0}
            ],
        });
    });
</script>
@endsection