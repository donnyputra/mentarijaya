@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("New User") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route("users.index") }}">{{ __("User") }}</a></li>
								<li class="breadcrumb-item active">{{ __("New User") }}</li>
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
					<!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

        </div>
    </div>
</div>
@endsection