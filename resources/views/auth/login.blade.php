@extends('layouts.auth')

@section('content')
<!-- Left Section - Green Background with Branding -->
<div class="auth-left">
    <div class="brand-logo">
        <img src="{{ asset('images/tracademics-logo.png') }}" alt="TracAdemics">
        Tracademics
    </div>
    <div class="brand-title">Academic Compliance Monitoring System</div>
    <div class="brand-description">
        Facilitating the efficient management of faculty requirements and timely submission of academic documentation.
    </div>
</div>

<!-- Right Section - White Background with Form -->
<div class="auth-right">
    <div class="form-container">
        <div class="form-header">
            <h2>Welcome Back</h2>
            <p>Please sign in to your account</p>
        </div>

        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <!-- Display Session Status -->
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label text-muted mb-2">Email Username</label>
                <div class="position-relative">
                    <div class="input-group">
                        <input 
                            id="username" 
                            type="text" 
                            class="form-control username-input @error('email') is-invalid @enderror" 
                            name="username" 
                            value="{{ old('username', str_replace('@brokenshire.edu.ph', '', old('email'))) }}" 
                            required 
                            autocomplete="username" 
                            autofocus
                            placeholder="Enter your username"
                            style="border-radius: 8px; padding-right: 160px; background-color: #f8f9fa;"
                        >
                        <span class="domain-suffix">@brokenshire.edu.ph</span>
                    </div>
                    <input type="hidden" id="email" name="email" value="">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label text-muted mb-2">Password</label>
                <div class="position-relative">
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        style="border-radius: 8px; background-color: #f8f9fa;"
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>
                Login
            </button>
        </form>

        <div class="email-notice">
            Only emails with brokenshire domain are allowed.
        </div>
    </div>
</div>

<style>
/* Custom styling for email username input with domain suffix */
.form-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-group .position-relative .input-group {
    position: relative;
}

.username-input {
    border: 2px solid #e9ecef;
    border-radius: 8px !important;
    background-color: #f8f9fa;
    padding: 12px 160px 12px 16px !important;
    font-size: 16px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.username-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    background-color: #fff;
}

.domain-suffix {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 16px;
    pointer-events: none;
    z-index: 10;
    background: transparent;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

/* Password field styling to match */
.form-control {
    border: 2px solid #e9ecef;
    padding: 12px 16px;
    font-size: 16px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    background-color: #fff;
}

/* Login button styling */
.btn-login {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    transition: all 0.15s ease-in-out;
}

.btn-login:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
}
</style>

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

// Handle email combination on form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="POST"]');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    
    form.addEventListener('submit', function(e) {
        const username = usernameInput.value.trim();
        if (username) {
            // Combine username with domain
            emailInput.value = username + '@brokenshire.edu.ph';
        }
    });
    
    // Also update email field on username input change for real-time validation
    usernameInput.addEventListener('input', function() {
        const username = this.value.trim();
        if (username) {
            emailInput.value = username + '@brokenshire.edu.ph';
        } else {
            emailInput.value = '';
        }
    });
});
</script>
@endsection
