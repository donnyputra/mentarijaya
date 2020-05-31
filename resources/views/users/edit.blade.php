@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("Edit User") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route("users.index") }}">{{ __("User") }}</a></li>
								<li class="breadcrumb-item active">{{ __("Edit User") }}</li>
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
					<!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

        </div>
    </div>
</div>
@endsection