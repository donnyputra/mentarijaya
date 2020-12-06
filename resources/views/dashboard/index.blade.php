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

                        <div class="col-4">
                            <div class="table-responsive">
                                <!-- BAR CHART -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><b>In Stock Item by Category</b></h3>

                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart">
                                            <canvas id="barChart"
                                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->

                            </div>
                        </div>
                        
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><b>In Stock Item by Gold Rate</b></h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table
                                        class="table table-hover table-responsive table-striped text-nowrap">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($totalWeightSummaryCollection as $row)
                                                <tr>
                                                    <td>{{ $row->item_gold_rate . '%' }}</td>
                                                    <td>{{ $row->A . ' gr' }}</td>
                                                    <td>{{ $row->CK . ' gr' }}</td>
                                                    <td>{{ $row->C . ' gr' }}</td>
                                                    <td>{{ $row->GL . ' gr' }}</td>
                                                    <td>{{ $row->K . ' gr' }}</td>
                                                    <td>{{ $row->L . ' gr' }}</td>
                                                    <td>{{ $row->PT . ' gr' }}</td>
                                                    <td>{{ $row->W . ' gr' }}</td>
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
                            <div class="table-responsive">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <button class="btn btn-secondary float-right" type="button"
                                                    data-toggle="modal" data-target="#advanceFilter"
                                                    aria-expanded="false" aria-controls="advanceFilter">
                                                    Filter</button>
                                            </div>
                                        </div>

                                        <div class="clearfix">
                                            <table class="table table-hover table-striped table-head-fixed text-nowrap">
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
                                                <tbody>
                                                    @foreach ($summaryCollection as $summary)
                                                    <tr>
                                                        <td>{{ Carbon\Carbon::parse($summary->sales_date)->format('d-M-Y') }}
                                                        </td>
                                                        <td>{{ $summary->item_gold_rate . "%" }}</td>
                                                        <td>{{ $summary->total_weight . " gr" }}</td>
                                                        <td>{{ $summary->total_sales == null ? "-" : ("Rp " . number_format($summary->total_sales, 2, ',', '.')) }}
                                                        </td>
                                                        <td>{{ $summary->average == null ? "-" : ("Rp " . number_format($summary->average, 2, ',', '.')) }}
                                                        </td>
                                                        <td>
                                                            @foreach (array_count_values(explode(',',
                                                            $summary->item_category_list)) as $soldKey => $soldValue)
                                                            <b>{{ $soldKey }}</b>({{ $soldValue }})
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div> <!-- ./Table Grid -->

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
<script type="text/javascript">
    var itemCountByCategoryData = {
        labels: [{!! $itemCountBarChartLabel !!}],
        datasets: [{
            label: 'Item Count',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            pointRadius: false,
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: [{{ $itemCountBarChartValue }}]
        }]
    };

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    var barChartData = $.extend(true, {}, itemCountByCategoryData);
    barChartData.datasets[0] = itemCountByCategoryData.datasets[0];

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
    };

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });
</script>
@endsection