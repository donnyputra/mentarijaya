@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <div class="col-12 mb-3">
                	
                	<div class="card uper">
					    <div class="card-header">
					        {{ __("Create New User") }}
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
					        <form method="POST" action="{{ route('users.store') }}">
					            <div class="form-group">
					                @csrf
					                <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" />
					            </div>
					            <div class="form-group">
					                <label for="username">Username</label>
					                <input type="text" class="form-control" name="username" value="{{ old('username') }}" />
					            </div>
					            <div class="form-group">
					                <label for="email">Email Address</label>
					                <input type="text" class="form-control" name="email" value="{{ old('email') }}" />
					            </div>
					            <div class="form-group">
					                <label for="password">Password</label>
					                <input type="password" class="form-control" name="password" />
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" />
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