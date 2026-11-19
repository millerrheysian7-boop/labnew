@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h2 class="auth-title">Forgot your password?</h2>
    <p class="auth-subtitle">Enter your email and we will send you a link to choose a new password.</p>

    @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="you@example.com" required autofocus>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="bi bi-send"></i> Email Password Reset Link
        </button>

        <div class="text-center">
            <a class="auth-link" href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Back to log in</a>
        </div>
    </form>
@endsection
