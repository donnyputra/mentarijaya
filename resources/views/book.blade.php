@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <h3>
                {{ __("Book") }}
                <small class="text-muted">{{ __("- Manage items detail, price, sales, etc.") }}</small>
            </h3>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            <!-- TODO: put content here -->

        </div>
    </div>
</div>
@endsection
