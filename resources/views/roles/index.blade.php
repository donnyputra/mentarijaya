@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                <div class="col-12 mb-3">
                    <h3>
                        {{ __("Manage Roles") }}
                        <small class="text-muted">{{ __("- View, Create, Update your roles.") }}</small>
                    </h3>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- <a class="btn btn-primary float-right mb-3" href="{{ route('roles.create') }}" role="button">{{ __("Add New") }}</a> -->
                    <p class="text-muted">{{ __("This is predefined options by system. It can't be changed.") }}</p>
                    <br/>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @livewire('roles-table')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
