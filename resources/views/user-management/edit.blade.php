@extends('layouts.app')

@section('title', 'Edit User - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit User</h1>
            <p class="text-muted">Update user information and permissions</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">Edit {{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        User Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- User Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Role and Department -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Faculty Type (conditional) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faculty_type" class="form-label">Faculty Type</label>
                                    <select name="faculty_type" id="faculty_type" class="form-control @error('faculty_type') is-invalid @enderror">
                                        <option value="">Not applicable</option>
                                        <option value="regular" {{ old('faculty_type', $user->faculty_type) == 'regular' ? 'selected' : '' }}>Full-time</option>
                                        <option value="part-time" {{ old('faculty_type', $user->faculty_type) == 'part-time' ? 'selected' : '' }}>Part-time</option>
                                    </select>
                                    @error('faculty_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Reset Section -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Password Reset (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                            <small class="form-text text-muted">Leave blank to keep current password</small>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                               value="1" {{ old('is_active', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">
                                            Active Account
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Display -->
                        <div class="card bg-info bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6>Account Information:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Created:</strong> {{ $user->created_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                </div>
                                @if($user->last_login_at)
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            <strong>Last Login:</strong> {{ $user->last_login_at->format('M d, Y h:i A') }} 
                                            ({{ $user->last_login_at->diffForHumans() }})
                                        </small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide faculty type based on role selection
document.getElementById('role_id').addEventListener('change', function() {
    const facultyTypeField = document.getElementById('faculty_type');
    const facultyTypeDiv = facultyTypeField.closest('.mb-3');
    const selectedRole = this.options[this.selectedIndex].text.trim();
    const isFaculty = selectedRole === 'Faculty';
    const isProgramHead = selectedRole === 'Program Head';

    facultyTypeDiv.style.display = isFaculty ? 'block' : 'none';

    if (isFaculty) {
        facultyTypeField.disabled = false;
        return;
    }

    if (isProgramHead) {
        facultyTypeField.value = 'regular';
        facultyTypeField.disabled = false;
    } else {
        facultyTypeField.value = '';
        facultyTypeField.disabled = true;
    }
});

// Trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('role_id').dispatchEvent(new Event('change'));
});
</script>
@endsection
