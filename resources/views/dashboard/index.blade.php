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
                                            <table
                                                class="table table-hover table-striped table-head-fixed text-nowrap">
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
                                                        <td>{{ Carbon\Carbon::parse($summary->sales_date)->format('d-M-Y') }}</td>
                                                        <td>{{ $summary->item_gold_rate . "%" }}</td>
                                                        <td>{{ $summary->total_weight . " gr" }}</td>
                                                        <td>{{ $summary->total_sales == null ? "-" : ("Rp " . number_format($summary->total_sales, 2, ',', '.')) }}</td>
                                                        <td>{{ $summary->average == null ? "-" : ("Rp " . number_format($summary->average, 2, ',', '.')) }}</td>
                                                        <td>
                                                            @foreach (array_count_values(explode(',', $summary->item_category_list)) as $soldKey => $soldValue)
                                                                <b>{{ $soldKey }}</b>({{ $soldValue }})
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div> <!-- ./Table Grid -->

                                        <div class="row">
                                            <div class="col">
                                                {{ $summaryCollection->appends(request()->input())->links() }}
                                            </div>

                                            <div class="col text-right text-muted">
                                                Showing {{ $summaryCollection->firstItem() }} to {{ $summaryCollection->lastItem() }} out of
                                                {{ $summaryCollection->total() }} results
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
    </div>
</div>
@endsection