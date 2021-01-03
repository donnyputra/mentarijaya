@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Item") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Item") }}</li>
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
                                <a class="btn btn-primary mb-3" href="{{ route('items.employee.create') }}" role="button"><span><i class="nav-icon fas fa-plus"></i></span></a>
                                {{-- <a class="btn btn-secondary mb-3" href="{{ route('items.bulkupload') }}" role="button">{{ __("Bulk Upload") }}</a> --}}
                            </div> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="table-responsive">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">TOTAL WEIGHT PER CATEGORY</h3>

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
                                                    <th>Category Code</th>
                                                    <th>Category Name</th>
                                                    <th>Total Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($summary_total_weight_per_category as $row)
                                                <tr>
                                                    <td>{{ $row->category_code }}</td>
                                                    <td>{{ $row->category_name }}</td>
                                                    <td>{{ StringHelper::formatDecimalDisplay($row->sum_weight) }} gr</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="table-responsive">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">COUNT ITEM PER CATEGORY</h3>

                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-responsive table-hover table-striped text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Category Code</th>
                                                    <th>Category Name</th>
                                                    <th>Item Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($summary_total_count_per_category as $row)
                                                <tr>
                                                    <td>{{ $row->category_code }}</td>
                                                    <td>{{ $row->category_name }}</td>
                                                    <td>{{ $row->count_item }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <div class="card">
                                    <div class="card-body">
                                        @livewire('employee-items-table')
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
