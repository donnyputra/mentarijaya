@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Profile") }}</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item">{{ __("Profile") }}</li>
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
                                        <form method="POST" action="{{ route('profile.update') }}">
                                            @csrf
                                            <div class="form-group" hidden>
                                                <label for="id">{{ __('ID') }}</label>
                                                <input id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ Auth::user()->id }}" autocomplete="id">
                                            </div>
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}</label>
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ Auth::user()->name }}" required autocomplete="name" autofocus>
                                            </div>
                                            <div class="form-group">
                                                <label for="username">{{ __('Username') }}</label>
                                                <input id="username" type="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ Auth::user()->username }}" required autocomplete="username" @if(Auth::user()->username == 'admin') readonly @endif>
                                            </div>                    
                                            <div class="form-group">
                                                <label for="email">{{ __('E-Mail Address') }}</label>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email }}" required autocomplete="email">
                                            </div>
                    
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button> 
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
