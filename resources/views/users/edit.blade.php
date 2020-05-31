@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <div class="col-12 mb-3">
                	
                	<div class="card uper">
					    <div class="card-header">
					        {{ __("Update User") }}
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
                            <form method="POST" action="{{ route('users.update', $user->id) }}">
                                <div class="form-group">
                                    @csrf
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" value="{{ $user->username }}" @if($user->username == 'admin') readonly @endif />
                                </div>
					            <div class="form-group">
					                <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" />
					            </div>
					            <div class="form-group">
					                <label for="email">Email Address</label>
					                <input type="text" class="form-control" name="email" value="{{ $user->email }}" />
								</div>
								@if($user->username == 'admin')
								<div class="form-group">
									<label for="role_id">Role</label>
									<input type="text" class="form-control" name="role_id" value="{{ $role->id }}" hidden />
									<input type="text" class="form-control" name="role_name" value="{{ $role->name }}" readonly />
								</div>
								@else
								<div class="form-group">
									<label for="role_id">Role</label>
									<div>
										<select class="form-control" name="role_id">
											<option></option>
											@foreach ($roles as $role)
												<option value="{{ $role->id }}" {{ $role->id == $userRole->role_id ? 'selected' : '' }}>{{ $role->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								@endif

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