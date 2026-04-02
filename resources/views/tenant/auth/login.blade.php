@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', $school->school_name . ' — Login')

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

          {{-- Brand --}}
          <div class="app-brand justify-content-center mb-6">
            <a href="javascript:void(0);" class="app-brand-link gap-2">
              <i class="icon-base ti tabler-building" style="font-size:2rem; color: var(--bs-primary)"></i>
              <span class="app-brand-text demo text-heading fw-bold">
                {{ $school->school_name }}
              </span>
            </a>
          </div>

          <h4 class="mb-1">Welcome back! 👋</h4>
          <p class="mb-6 text-muted">Sign in to your school portal to continue.</p>

          {{-- Errors --}}
          @if($errors->any())
            <div class="alert alert-danger p-2 mb-4">
              <ul class="mb-0 list-unstyled">
                @foreach($errors->all() as $error)
                  <li>
                    <i class="icon-base ti tabler-exclamation-circle me-1"></i>{{ $error }}
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Session Status --}}
          @if(session('status'))
            <div class="alert alert-success p-2 mb-4">
              {{ session('status') }}
            </div>
          @endif

          <form id="formAuthentication" class="mb-4"
                action="{{ route('tenant.login.submit') }}" method="POST">
            @csrf

            {{-- Email --}}
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email Address</label>
              <input type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     id="email"
                     name="email"
                     placeholder="your@email.com"
                     value="{{ old('email') }}"
                     autofocus
                     required />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Password --}}
            <div class="mb-6 form-password-toggle form-control-validation">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password"
                      id="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password"
                      placeholder="············"
                      required />
                <span class="input-group-text cursor-pointer">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Remember Me --}}
            <div class="my-8">
              <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox"
                       id="remember-me" name="remember" />
                <label class="form-check-label" for="remember-me">
                  Remember Me
                </label>
              </div>
            </div>

            {{-- Submit --}}
            <div class="mb-4">
              <button class="btn btn-primary d-grid w-100" type="submit">
                Sign In
              </button>
            </div>

          </form>

          <p class="text-center text-muted small mb-0">
            Having trouble? Contact
            <a href="mailto:{{ \App\Models\SaasSettings::get('support_email', 'support@saas.com') }}">
              {{ \App\Models\SaasSettings::get('support_email', 'support@saas.com') }}
            </a>
          </p>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection