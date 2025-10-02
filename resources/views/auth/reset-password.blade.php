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
        Create a new secure password for your account.
    </div>
</div>

<!-- Right Section - White Background with Form -->
<div class="auth-right">
    <div class="form-container">
        <div class="form-header">
            <h2>Reset Password</h2>
            <p>Enter your new password below</p>
        </div>

        <div class="user-avatar">
            <i class="fas fa-key"></i>
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

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Hidden token field -->
            <input type="hidden" name="token" value="{{ $token }}">

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
                <label for="password" class="form-label text-muted mb-2">New Password</label>
                <div class="position-relative">
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="Enter new password"
                        style="border-radius: 8px; background-color: #f8f9fa; padding-right: 45px;"
                    >
                    <button type="button" class="btn btn-link position-absolute" id="togglePassword" 
                            style="top: 50%; right: 10px; transform: translateY(-50%); border: none; background: none; color: #6c757d; z-index: 10;">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label text-muted mb-2">Confirm New Password</label>
                <div class="position-relative">
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        class="form-control" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                        style="border-radius: 8px; background-color: #f8f9fa; padding-right: 45px;"
                    >
                    <button type="button" class="btn btn-link position-absolute" id="togglePasswordConfirm" 
                            style="top: 50%; right: 10px; transform: translateY(-50%); border: none; background: none; color: #6c757d; z-index: 10;">
                        <i class="fas fa-eye" id="togglePasswordConfirmIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-reset">
                <i class="fas fa-save me-2"></i>
                Reset Password
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="back-to-login">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Login
            </a>
        </div>

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

/* Reset button styling */
.btn-reset {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.btn-reset:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
}

/* Back to login link */
.back-to-login {
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.15s ease-in-out;
}

.back-to-login:hover {
    color: #28a745;
    text-decoration: none;
}

/* Email notice styling */
.email-notice {
    text-align: center;
    color: #6c757d;
    font-size: 12px;
    margin-top: 20px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}
</style>

<script>
// Handle email combination on form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="POST"]');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    
    // Password toggle functionality for new password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    }
    
    // Password toggle functionality for confirm password
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const toggleConfirmIcon = document.getElementById('togglePasswordConfirmIcon');
    
    if (togglePasswordConfirm && passwordConfirmInput && toggleConfirmIcon) {
        togglePasswordConfirm.addEventListener('click', function() {
            if (passwordConfirmInput.type === 'password') {
                passwordConfirmInput.type = 'text';
                toggleConfirmIcon.classList.remove('fa-eye');
                toggleConfirmIcon.classList.add('fa-eye-slash');
            } else {
                passwordConfirmInput.type = 'password';
                toggleConfirmIcon.classList.remove('fa-eye-slash');
                toggleConfirmIcon.classList.add('fa-eye');
            }
        });
    }
    
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