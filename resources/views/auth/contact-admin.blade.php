@extends('layouts.auth')

@section('title', 'Contact Administrator - TracAdemics')

@section('content')
<div class="auth-container">
    <div class="auth-left">
        <div class="brand-logo">
            <img src="{{ asset('images/tracademics-logo.png') }}" alt="Brokenshire College" class="auth-logo">
            Tracademics
        </div>
        <div class="brand-title">Password Reset Assistance</div>
        <div class="brand-description">Contact the MIS administrator for secure password resets and verification.</div>
    </div>

    <div class="auth-right">
        <div class="form-container">
            <div class="text-center mb-3">
                <i class="fas fa-lock fa-3x text-primary mb-2"></i>
                <h4 class="mb-0">Password Reset Request</h4>
                <p class="text-muted small mb-0">Contact System Administrator</p>
            </div>

            <div class="alert alert-info d-flex align-items-start" role="alert">
                <i class="fas fa-info-circle me-2 mt-1"></i>
                <div>
                    <strong>Password Reset Policy</strong>
                    <div class="small text-muted">For security, faculty and staff password resets are handled by the MIS administrator.</div>
                </div>
            </div>

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-3">Please contact MIS and provide the information below to expedite your request.</p>

                    <div class="bg-light p-3 rounded mb-3">
                        <h6 class="mb-2"><i class="fas fa-phone me-2"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><strong>MIS Office:</strong><br><small class="text-muted">Room 201, Administration Building</small></p>
                                <p class="mb-2"><strong>Phone:</strong><br><small class="text-muted">(082) 123-4567 ext. 201</small></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><strong>Email:</strong><br><small class="text-muted">mis@brokenshire.edu.ph</small></p>
                                <p class="mb-2"><strong>Office Hours:</strong><br><small class="text-muted">Mon - Fri, 8:00 AM - 5:00 PM</small></p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>What to Provide:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Your full name</li>
                            <li>Your @brokenshire.edu.ph email address</li>
                            <li>Your department/program</li>
                            <li>Valid ID for verification</li>
                        </ul>
                    </div>

                    <div class="d-grid mt-3">
                        <a href="{{ route('login') }}" class="btn btn-login">
                            <i class="fas fa-arrow-left me-2"></i> Back to Login
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">Password resets are typically processed within 1 business day.</small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-2">
                <p class="text-muted small"><i class="fas fa-shield-alt me-1"></i> This policy ensures the security of all TracAdemics accounts.</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Minor tweaks to align with auth layout and prevent unwanted scrolling */
    html, body { height: 100%; }
    /* Prevent horizontal scrolling on auth pages */
    body { overflow-x: hidden; }

    .auth-container { 
        display: flex;
        width: 100%;
        min-height: 100vh;
        margin: 0;
        box-sizing: border-box;
    }

    /* Left and right panes take equal available space. Use flex:1 and
       min-width:0 to allow children to shrink (prevents overflow caused by
       long/large child elements inside flex items). */
    .auth-left, .auth-right {
        flex: 1 1 50%;
        min-width: 0; /* critical to prevent overflow in flex children */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        box-sizing: border-box;
    }

    /* Keep the left pane content vertically centered but allow the right pane
       to scroll internally when the viewport is short to avoid page-level
       scrolling and double-scrollbar issues. */
    .auth-left { align-items: center; }
    .auth-right { align-items: stretch; }

    /* Ensure the form card uses available space but doesn't cause page overflow.
       Give it an internal max-height and allow it to scroll internally if needed. */
    .form-container {
        max-width: 680px;
        width: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        max-height: calc(100vh - 4rem); /* account for padding */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Prevent any child content from forcing the flex item to overflow horizontally */
    .form-container * { min-width: 0; }

    .auth-logo { height: 56px; }
    .brand-title { font-size: 1.25rem; }
    .bg-light { background-color: #f8f9fa !important; }
    .btn-login { background: linear-gradient(90deg,#7fffd4 0%,#28a745 100%); color: #fff; border-radius: 24px; padding: 12px; }
    .btn-login:hover { transform: translateY(-2px); }
</style>
@endpush

@endsection