@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>User Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Add User
                </button>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row mb-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Users</h6>
                                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Users</h6>
                                    <h3 class="mb-0">{{ $stats['active'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-check fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">MIS Users</h6>
                                    <h3 class="mb-0">{{ $stats['mis'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-cog fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Faculty Users</h6>
                                    <h3 class="mb-0">{{ $stats['faculty'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-chalkboard-teacher fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">System Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Last Login</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">ID: {{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->role->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $user->department->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== Auth::id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                        No users found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($users) && method_exists($users, 'links'))
                        <div class="mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="email_username" placeholder="username">
                                <span class="input-group-text">@brokenshire.edu.ph</span>
                                <input type="hidden" id="email" name="email">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-control" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" id="program-container" style="display: none;">
                            <label for="program_id" class="form-label">Program</label>
                            <select class="form-control" id="program_id" name="program_id">
                                <option value="">Select Program</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3" id="faculty-type-container" style="display: none;">
                            <label for="faculty_type" class="form-label">Faculty Type</label>
                            <select class="form-control" id="faculty_type" name="faculty_type">
                                <option value="">Select Type</option>
                                <option value="regular">Regular</option>
                                <option value="visiting">Visiting</option>
                                <option value="part-time">Part-time</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('password_confirmation', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Password Requirements:</h6>
                        <ul class="mb-0">
                            <li>At least 8 characters long</li>
                            <li>Must contain at least one uppercase letter (A-Z)</li>
                            <li>Must contain at least one lowercase letter (a-z)</li>
                            <li>Must contain at least one number (0-9)</li>
                            <li>Both password fields must match</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Register User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Password visibility toggle function
function togglePasswordVisibility(fieldId, button) {
    const passwordField = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const emailUsername = document.getElementById('email_username');
    const emailHidden = document.getElementById('email');
    const roleSelect = document.getElementById('role_id');
    const departmentSelect = document.getElementById('department_id');
    const programSelect = document.getElementById('program_id');
    const programContainer = document.getElementById('program-container');
    const facultyTypeContainer = document.getElementById('faculty-type-container');
    
    // Update hidden email field when username changes
    emailUsername.addEventListener('input', function() {
        emailHidden.value = this.value + '@brokenshire.edu.ph';
    });
    
    // Show/hide program and faculty type based on role
    roleSelect.addEventListener('change', function() {
        const selectedRole = this.options[this.selectedIndex].text;
        
        if (selectedRole === 'Faculty' || selectedRole === 'Program Head') {
            programContainer.style.display = 'block';
            facultyTypeContainer.style.display = 'block';
        } else if (selectedRole === 'Dean') {
            programContainer.style.display = 'none';
            facultyTypeContainer.style.display = 'none';
            programSelect.value = '';
        } else {
            programContainer.style.display = 'none';
            facultyTypeContainer.style.display = 'none';
            programSelect.value = '';
        }
    });
    
    // Update programs when department changes
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        programSelect.innerHTML = '<option value="">Select Program</option>';
        
        if (departmentId) {
            const departments = @json($departments);
            const selectedDept = departments.find(dept => dept.id == departmentId);
            
            if (selectedDept && selectedDept.programs) {
                selectedDept.programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.id;
                    option.textContent = program.name;
                    programSelect.appendChild(option);
                });
            }
        }
    });
    
    // Password validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    function validatePassword() {
        const pwd = password.value;
        const confirm = passwordConfirm.value;
        
        // Check password requirements
        const hasLower = /[a-z]/.test(pwd);
        const hasUpper = /[A-Z]/.test(pwd);
        const hasNumber = /\d/.test(pwd);
        const isLongEnough = pwd.length >= 8;
        const passwordsMatch = pwd === confirm;
        
        // Validate password field
        if (pwd && (!hasLower || !hasUpper || !hasNumber || !isLongEnough)) {
            password.classList.add('is-invalid');
            password.nextElementSibling.textContent = 'Password must contain uppercase, lowercase, number and be 8+ characters';
        } else {
            password.classList.remove('is-invalid');
            password.nextElementSibling.textContent = '';
        }
        
        // Validate confirmation field
        if (confirm && !passwordsMatch) {
            passwordConfirm.classList.add('is-invalid');
            passwordConfirm.nextElementSibling.textContent = 'Passwords do not match';
        } else {
            passwordConfirm.classList.remove('is-invalid');
            passwordConfirm.nextElementSibling.textContent = '';
        }
    }
    
    password.addEventListener('input', validatePassword);
    passwordConfirm.addEventListener('input', validatePassword);
    
    // Show validation errors if form was submitted and has errors
    @if($errors->any())
        const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
        modal.show();
        
        @foreach($errors->all() as $error)
            console.log('{{ $error }}');
        @endforeach
    @endif
});
</script>
@endsection
