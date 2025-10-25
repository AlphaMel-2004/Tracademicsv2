@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-cog me-2"></i>Profile Settings</h2>
            </div>
            <div class="row">
                <!-- Profile Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            class="form-control @error('name') is-invalid @enderror" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name', $user->name) }}" 
                                            required
                                        >
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="email" 
                                            value="{{ $user->email }}" 
                                            disabled
                                        >
                                        <small class="text-muted">Email cannot be changed for security reasons.</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="role" 
                                            value="{{ $user->role->name }}" 
                                            disabled
                                        >
                                        <small class="text-muted">Role is managed by administrators.</small>
                                    </div>

                                    @if($user->department)
                                    <div class="col-md-6 mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="department" 
                                            value="{{ $user->department->name }}" 
                                            disabled
                                        >
                                        <small class="text-muted">Department is managed by administrators.</small>
                                    </div>
                                    @endif
                                </div>

                                @if($user->program)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="program" class="form-label">Program</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="program" 
                                            value="{{ $user->program->name }}" 
                                            disabled
                                        >
                                        <small class="text-muted">Program is managed by administrators.</small>
                                    </div>
                                </div>
                                @endif

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Member Since:</strong><br>
                                <span class="text-muted">{{ $user->created_at->format('F d, Y') }}</span>
                            </div>

                            @if($user->last_login_at)
                            <div class="mb-3">
                                <strong>Last Login:</strong><br>
                                <span class="text-muted">{{ $user->last_login_at->format('F d, Y g:i A') }}</span>
                            </div>
                            @endif

                            <div class="mb-3">
                                <strong>Account Status:</strong><br>
                                <span class="badge bg-success">Active</span>
                            </div>

                            <hr>

                            <div class="d-grid">
                                <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
