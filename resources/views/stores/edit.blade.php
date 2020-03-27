@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <div class="col-12 mb-3">
                    
                    <div class="card uper">
                        <div class="card-header">
                            {{ __("Edit Store") }}
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

                            <form method="post" action="{{ route('stores.update', $store->id) }}">
                                <div class="form-group">
                                    @csrf
                                    <label for="store_code">Store Code</label>
                                    <input type="text" class="form-control" name="store_code" value="{{ $store->code }}" />
                                </div>
                                <div class="form-group">
                                    <label for="store_name">Store Name</label>
                                    <input type="text" class="form-control" name="store_name" value="{{ $store->name }}" />
                                </div>
                                <div class="form-group">
                                    <label for="store_address">Address</label>
                                    <textarea class="form-control" name="store_address" rows="3">{{ $store->address }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="store_phone">Phone No</label>
                                    <input type="text" class="form-control" name="store_phone" value="{{ $store->phone_no }}" />
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