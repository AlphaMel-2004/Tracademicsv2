@extends('layouts.auth')

@section('title', 'Contact Administrator - TracAdemics')

@section('content')
<div class="auth-card">
    <div class="text-center mb-4">
        <img src="{{ asset('images/brokenshire-logo.png') }}" alt="Brokenshire College" class="auth-logo">
        <h1 class="auth-title">Password Reset Request</h1>
        <p class="auth-subtitle text-muted">Contact System Administrator</p>
    </div>

    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Password Reset Policy</strong>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                <h5>Need to Reset Your Password?</h5>
            </div>

            <div class="mb-4">
                <p class="text-muted">
                    For security reasons, password resets for faculty and staff accounts are handled directly by the 
                    <strong>Management Information System (MIS)</strong> administrator.
                </p>
            </div>

            <div class="bg-light p-3 rounded mb-4">
                <h6 class="mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>MIS Office:</strong><br>
                            <small class="text-muted">Room 201, Administration Building</small>
                        </p>
                        <p class="mb-2">
                            <strong>Phone:</strong><br>
                            <small class="text-muted">(082) 123-4567 ext. 201</small>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            <small class="text-muted">mis@brokenshire.edu.ph</small>
                        </p>
                        <p class="mb-2">
                            <strong>Office Hours:</strong><br>
                            <small class="text-muted">Monday - Friday, 8:00 AM - 5:00 PM</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>What to Provide:</strong>
                <ul class="mb-0 mt-2">
                    <li>Your full name</li>
                    <li>Your @brokenshire.edu.ph email address</li>
                    <li>Your department/program</li>
                    <li>Valid ID for verification</li>
                </ul>
            </div>

            <div class="d-grid">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                </a>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Password resets are typically processed within 1 business day
                </small>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted small">
            <i class="fas fa-shield-alt me-1"></i>
            This policy ensures the security of all TracAdemics accounts
        </p>
    </div>
</div>

@push('styles')
<style>
    .auth-card {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .auth-logo {
        height: 60px;
        width: auto;
        margin-bottom: 1rem;
    }
    
    .auth-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .auth-subtitle {
        font-size: 1rem;
        margin-bottom: 0;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .alert-info {
        border-left: 4px solid #0dcaf0;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    .alert-warning {
        border-left: 4px solid #ffc107;
        background-color: #fff3cd;
        border-color: #ffdf7e;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
</style>
@endpush
@endsection