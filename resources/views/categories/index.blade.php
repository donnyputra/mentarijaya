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
                        {{ __("Manage Categories") }}
                        <small class="text-muted">{{ __("- View, Create, Update your categories.") }}</small>
                    </h3>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-primary float-right mb-3" href="{{ route('categories.create') }}" role="button">{{ __("Add New") }}</a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @livewire('categories-table')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection