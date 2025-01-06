@extends('layouts.simple')



@section('content')
<div class="bg-image" style="background-image: url('{{asset('media/photos/c-site3.jpg')}}');">
  <div class="row mx-0 bg-black-50">
    <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
      <div class="p-4">
        <p class="fs-3 fw-semibold text-white">
          To serve more customer, better, faster and at less cost.
        </p>
        <p class="text-white-75 fw-medium">
          Copyright &copy; <span data-toggle="year-copy"></span>
        </p>
      </div>
    </div>
    <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-body-extra-light">
      <div class="content content-full">
        <!-- Header -->
        <div class="px-4 py-2 mb-4">
          <a class="link-fx fw-bold" href="/">
            <img class="mb-3" src="{{asset('media/logo.png')}}" width="300px" alt="multiline logo">
          </a>
          <h1 class="h3 fw-semibold mt-4 mb-2">Welcome to <span class="text-danger">Tools and Equipment Monitoring System</span></h1>
          <h2 class="h5 fw-medium text-muted mb-0">Please sign in</h2>
        </div>
        {{-- <button type="button" class="js-notify btn btn-sm btn-alt-danger" data-type="danger" data-icon="fa fa-times" data-message="Update failed! :-(">Error</button> --}}
        <!-- END Header -->

        <!-- Sign In Form -->
        <!-- jQuery Validation functionality is initialized with .js-validation-signin class in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js -->
        <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
        <form class="js-validation-signin px-4" action="{{route('login')}}" method="POST">
          @csrf
          <div class="form-floating mb-4">
            @if ($errors->has('email'))
              <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
            <input type="text" class="form-control @error('login_error') is-invalid @enderror" id="login_username" name="login_username" placeholder="Enter your username" required value="{{ old('login_username') }}">
            <label class="form-label" for="login-username">Username</label>
          </div>
          <div class="form-floating mb-4">
            @if ($errors->has('password'))
              <span class="text-danger">{{ $errors->first('password') }}</span>
            @endif
            <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Enter your password" required>
            <label class="form-label" for="login-password">Password</label>
          </div>
          {{-- <div class="mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="login-remember-me" name="login-remember-me" checked>
              <label class="form-check-label" for="login-remember-me">Remember Me</label>
            </div>
          </div> --}}
          <div class="mb-4">
            <button type="submit" class="btn btn-lg btn-alt-primary fw-semibold">
              Sign In
            </button>
            <div class="mt-4">
              <a class="fs-sm fw-medium link-fx text-muted me-2 mb-1 d-inline-block" href="op_auth_reminder2.html">
                Forgot Password
              </a>
            </div>
          </div>
        </form>
        <!-- END Sign In Form -->
      </div>
    </div>
  </div>
</div>
@endsection
