@extends('layouts.auth')

@section('content')
<!-- Left Section - Green Background with Branding -->
<div class="auth-left">
    <div class="brand-logo">
        <img src="{{ asset('images/tracademics-logo.svg') }}" alt="TracAdemics">
        Tracademics
    </div>
    <div class="brand-title">Academic Compliance Monitoring System</div>
    <div class="brand-description">
        Streamlining and organizing faculty requirements and document submissions.
    </div>
</div>

<!-- Right Section - White Background with Form -->
<div class="auth-right">
    <div class="form-container">
        <div class="form-header">
            <h2>Create Account</h2>
            <p>Join the academic compliance system</p>
        </div>

        <div class="user-avatar">
            <i class="fas fa-user-plus"></i>
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input 
                        id="name" 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autocomplete="name" 
                        autofocus
                        placeholder="Full Name"
                    >
                </div>
                @error('name')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="username                    @brokenshire.edu.ph"
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="Password"
                    >
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        id="password-confirm" 
                        type="password" 
                        class="form-control" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Confirm Password"
                    >
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('password-confirm', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <small>New accounts are automatically assigned the Faculty role.</small>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-user-plus me-2"></i>
                Create Account
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-2">Already have an account?</p>
            <a href="{{ route('login') }}" class="forgot-link">
                Sign in here
            </a>
        </div>

        <div class="email-notice">
            Only emails ending with @brokenshire.edu.ph are allowed.
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, button) {
    const passwordInput = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
