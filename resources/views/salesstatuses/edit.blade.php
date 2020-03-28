@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <div class="col-12 mb-3">
                    
                    <div class="card uper">
                        <div class="card-header">
                            {{ __("Edit Sales Status") }}
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <br /> @endif

                            <form method="post" action="{{ route('salesstatuses.update', $salesstatus->id) }}">
                                <div class="form-group">
                                    @csrf
                                    <label for="salesstatus_code">Sales Status Code</label>
                                    <input type="text" class="form-control" name="salesstatus_code" value="{{ $salesstatus->code }}" />
                                </div>
                                <div class="form-group">
                                    <label for="salesstatus_description">Sales Status Description</label>
                                    <textarea class="form-control" name="salesstatus_description" rows="3">{{ $salesstatus->description }}</textarea>
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
    </div>
</div>
@endsection