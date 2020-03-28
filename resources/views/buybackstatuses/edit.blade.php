@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <div class="col-12 mb-3">
                    
                    <div class="card uper">
                        <div class="card-header">
                            {{ __("Edit Buyback Status") }}
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
    </div>
</div>
@endsection