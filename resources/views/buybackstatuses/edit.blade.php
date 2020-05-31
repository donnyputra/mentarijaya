@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Edit Buyback Status") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route("buybackstatuses.index") }}">{{ __("Buyback Status") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Edit Buyback Status") }}</li>
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
                                        <form method="post" action="{{ route('buybackstatuses.update', $buybackstatus->id) }}">
                                            <div class="form-group">
                                                @csrf
                                                <label for="buybackstatus_code">Buyback Status Code</label>
                                                <input type="text" class="form-control" name="buybackstatus_code" value="{{ $buybackstatus->code }}" />
                                            </div>
                                            <div class="form-group">
                                                <label for="buybackstatus_description">Buyback Status Description</label>
                                                <textarea class="form-control" name="buybackstatus_description" rows="3">{{ $buybackstatus->description }}</textarea>
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