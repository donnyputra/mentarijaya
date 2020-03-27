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
                        {{ __("Manage Stores") }}
                        <small class="text-muted">{{ __("- View, Create, Update your stores.") }}</small>
                    </h3>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-primary float-right mb-3" href="{{ route('stores.create') }}" role="button">{{ __("Add New") }}</a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @if($stores->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>@sortablelink('code', 'Store Code')</th>
                                        <th>@sortablelink('name', 'Store Name')</th>
                                        <th>@sortablelink('phone_no', 'Phone No')</th>
                                        <th>@sortablelink('address', 'Address')</th>
                                        <th>{{ __("Action") }}</th>
                                        <!-- <th>@sortablelink('created_at', 'Created At')</th>
                                        <th>@sortablelink('updated_at', 'Updated At')</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stores as $store)
                                    <tr>
                                        <td>{{ $store->code }}</td>
                                        <td>{{ $store->name }}</td>
                                        <td>{{ $store->phone_no }}</td>
                                        <td>{{ $store->address }}</td>
                                        <td>
                                            <a href="{{ route('stores.edit', $store->id) }}"><span><i class="fa fa-edit"></i></span></a>
                                            <a href="{{ route('stores.delete', $store->id) }}" 
                                                onclick="event.preventDefault();
                                                     document.getElementById('delete-store-form-{{ $store->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                                            <form id="delete-store-form-{{ $store->id }}" method="POST" action="{{ route('stores.delete', $store->id) }}">
                                                @csrf
                                                <input type="text" class="form-control" name="id" value="{{ $store->id }}" hidden />
                                            </form>
                                        </td>
                                        <!-- <td>{{ $store->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $store->updated_at->format('d-m-Y') }}</td> -->
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $stores->appends(\Request::except('page'))->render() }}
                        @else
                            {{ __("No Data is found.") }}
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
