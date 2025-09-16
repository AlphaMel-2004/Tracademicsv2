@extends('layouts.app')

@section('title', 'User Details - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">User Details</h1>
            <p class="text-muted">View detailed information about this user</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- User Information Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- User Avatar -->
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    
                    <!-- User Basic Info -->
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    
                    <!-- Role Badge -->
                    @if($user->role)
                        <span class="badge bg-primary fs-6 mb-3">{{ $user->role->name }}</span>
                    @endif
                    
                    <!-- Status -->
                    <div class="mb-3">
                        @if($user->email_verified_at)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Pending Verification
                            </span>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit User
                        </a>
                        @if($user->id !== Auth::id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i>Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detailed Information -->
        <div class="col-md-8">
            <div class="row">
                <!-- Personal Details -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Personal Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Full Name:</label>
                                        <p class="mb-0">{{ $user->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Address:</label>
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">User ID:</label>
                                        <p class="mb-0">{{ $user->id }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Role:</label>
                                        <p class="mb-0">
                                            @if($user->role)
                                                <span class="badge bg-primary">{{ $user->role->name }}</span>
                                            @else
                                                <span class="text-muted">No role assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Department:</label>
                                        <p class="mb-0">
                                            @if($user->department)
                                                {{ $user->department->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Faculty Type:</label>
                                        <p class="mb-0">
                                            @if($user->faculty_type)
                                                <span class="badge bg-info">{{ ucfirst($user->faculty_type) }}</span>
                                            @else
                                                <span class="text-muted">Not applicable</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Account Created:</label>
                                        <p class="mb-0">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Last Updated:</label>
                                        <p class="mb-0">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Verified:</label>
                                        <p class="mb-0">
                                            @if($user->email_verified_at)
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $user->email_verified_at->format('M d, Y h:i A') }}
                                                </span>
                                            @else
                                                <span class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>Not verified
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Last Login:</label>
                                        <p class="mb-0">
                                            @if($user->last_login_at)
                                                {{ $user->last_login_at->format('M d, Y h:i A') }}
                                                <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Never logged in</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Compliance Summary (if applicable) -->
                @if($user->role && $user->role->name === 'Faculty')
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>Compliance Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($user->complianceSubmissions && $user->complianceSubmissions->count() > 0)
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="text-primary">{{ $user->complianceSubmissions->count() }}</h4>
                                        <small class="text-muted">Total Submissions</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-success">{{ $user->complianceSubmissions->where('status', 'approved')->count() }}</h4>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning">{{ $user->complianceSubmissions->where('status', 'pending')->count() }}</h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning">{{ $user->complianceSubmissions->where('status', 'needs_revision')->count() }}</h4>
                                        <small class="text-muted">Needs Revision</small>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted text-center">No compliance submissions yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to User Management
        </a>
    </div>
</div>
@endsection
