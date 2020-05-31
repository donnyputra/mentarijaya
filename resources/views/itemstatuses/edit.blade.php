@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Edit Item Status") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route("itemstatuses.index") }}">{{ __("Item Status") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Edit Item Status") }}</li>
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
                                        <form method="post" action="{{ route('itemstatuses.update', $itemstatus->id) }}">
                                            <div class="form-group">
                                                @csrf
                                                <label for="itemstatus_code">Item Status Code</label>
                                                <input type="text" class="form-control" name="itemstatus_code" value="{{ $itemstatus->code }}" />
                                            </div>
                                            <div class="form-group">
                                                <label for="itemstatus_description">Item Status Description</label>
                                                <textarea class="form-control" name="itemstatus_description" rows="3">{{ $itemstatus->description }}</textarea>
                                            </div>
            
                                            <div class="float-right">
                                                <a class="btn btn-secondary" href="{{ url()->previous() }}" role="button">{{ __("Back") }}</a>
                                                <button type="submit" class="btn btn-primary">{{ __("Save") }}</button>
                                            </div>
                                        </form>
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