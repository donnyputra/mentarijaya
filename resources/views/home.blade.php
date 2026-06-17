@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ isset($salesSummary) ? __("Dashboard") : __("Home") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                @if(isset($salesSummary))
                                <li class="breadcrumb-item active">{{ __("Dashboard") }}</li>
                                @endif
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            @isset($salesSummary)
                                @include('dashboard._sales_summary', ['salesSummary' => $salesSummary])
                            @else
                                <div class="table-responsive">
                                    <div class="card">
                                        <div class="card-body">
                                            You are logged in!
                                        </div>
                                    </div>
                                </div>
                            @endisset
                        </div>
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

        </div>
    </div>
</div>
@endsection
