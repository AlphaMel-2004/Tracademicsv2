@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('images/tracademics-logo.png') }}" alt="TracAdemics">
            Tracademics
        </div>
        <div class="brand-title">Academic Compliance Monitoring System</div>
        <div class="brand-description">Reset your password to continue accessing your academic management tools.</div>
    </div>

    <div class="auth-right">
        <div class="form-container">
            <div class="form-header">
                <h2>Forgot Password</h2>
                <p>Enter your Brokenshire email username and we'll send you a secure reset link.</p>
            </div>

            <div class="user-avatar">
                <i class="fas fa-unlock-alt"></i>
            </div>

            {{-- Status message --}}
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Email username</label>
                    <div class="input-group position-relative">
                        <input id="username" name="username" type="text" required
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('username', str_replace('@brokenshire.edu.ph', '', old('email'))) }}"
                            placeholder="your.username" autofocus>
                        <span class="position-absolute" style="right:12px;top:50%;transform:translateY(-50%);color:#6c757d">@brokenshire.edu.ph</span>
                    </div>
                    <input type="hidden" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">Send Reset Link</button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('login') }}" class="text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
                <small class="text-muted">Only brokenshire.edu.ph emails are allowed.</small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Two-column full-width auth layout to match login page */
    html, body { height: 100%; }
    body { overflow-x: hidden; }
    .auth-container { display:flex; width:100%; min-height:100vh; margin:0; }
    .auth-left, .auth-right { flex:0 0 50%; display:flex; align-items:center; justify-content:center; padding:2rem; box-sizing:border-box; }
    .form-container { max-width:680px; width:100%; }
    .user-avatar { width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg,#7fffd4,#28a745); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }
    .user-avatar i { color:#fff; }
</style>
@endpush

@push('scripts')
<script>
    // Combine username + domain into hidden email input before submit
    document.addEventListener('DOMContentLoaded', function () {
    // Select the POST form on this page (the reset form)
    const form = document.querySelector('form[method="POST"]');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');

        if (!form || !usernameInput || !emailInput) return;

        form.addEventListener('submit', function (e) {
            const username = usernameInput.value.trim();
            if (username) {
                emailInput.value = username + '@brokenshire.edu.ph';
            }
        });

        // keep hidden email updated for instant validation
        usernameInput.addEventListener('input', function () {
            const username = this.value.trim();
            emailInput.value = username ? username + '@brokenshire.edu.ph' : '';
        });
    });
</script>
@endpush

@endsection