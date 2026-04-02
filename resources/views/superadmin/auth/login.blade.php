@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Super Admin HQ - Login')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card border-0 shadow-lg">
        <div class="card-body">
          <div class="app-brand justify-content-center mb-6">
            <a href="javascript:void(0);" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">SaaS Master HQ</span>
            </a>
          </div>
          <h4 class="mb-1">HQ Access Restricted 🔒</h4>
          <p class="mb-6">Please sign-in with your Super Admin credentials.</p>

          @if($errors->any())
            <div class="alert alert-danger p-2 mb-4">
              <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                  <li><i class="icon-base ti tabler-exclamation-circle me-1"></i>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form id="formAuthentication" class="mb-4" action="{{ route('superadmin.login.submit') }}" method="POST">
            @csrf
            
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Admin Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                placeholder="superadmin@saas.com" value="{{ old('email') }}" autofocus required />
            </div>
            
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            
            <div class="my-8">
              <div class="d-flex justify-content-between">
                <div class="form-check mb-0 ms-2">
                  <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                  <label class="form-check-label" for="remember-me"> Remember Me </label>
                </div>
              </div>
            </div>
            
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Secure Login</button>
            </div>
          </form>

        </div>
      </div>
      </div>
  </div>
</div>
@endsection