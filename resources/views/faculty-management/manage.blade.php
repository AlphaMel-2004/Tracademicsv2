@extends('layouts.app')

@section('title', 'Manage Faculty')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-users-cog me-2"></i>Manage Faculty</h2>
            <p class="text-muted">Manage faculty members for {{ $program->name }}</p>
        </div>
    </div>

    <!-- Program Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $program->name }}</h4>
                            <p class="mb-0">{{ $program->description ?? 'Program Overview' }}</p>
                            <small>Department: {{ $program->department->name }}</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $allFaculty->count() }} Faculty Members</h3>
                            <button class="btn btn-light btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#registerFacultyModal">
                                <i class="fas fa-user-plus me-1"></i>Register Faculty User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Faculty Members</h5>
        </div>
        <div class="card-body">
            @if($allFaculty->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Faculty Member</th>
                                <th>Email</th>
                                <th>Faculty Type</th>
                                <th>Subject Assignments</th>
                                <th>Last Login</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allFaculty as $faculty)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial bg-success text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($faculty->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $faculty->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $faculty->role->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $faculty->email }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $faculty->faculty_type ? ucfirst($faculty->faculty_type) : 'Regular' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $subjectCount = $faculty->facultyAssignments->where('program_id', $program->id)->count();
                                    @endphp
                                    
                                    @if($subjectCount > 0)
                                        <span class="badge bg-info">{{ $subjectCount }} Subjects</span>
                                        <br>
                                        <small class="text-muted">
                                            @foreach($faculty->facultyAssignments->where('program_id', $program->id)->take(2) as $assignment)
                                                {{ $assignment->subject->name ?? 'Unknown' }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($subjectCount > 2)
                                                <br>... and {{ $subjectCount - 2 }} more
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No subjects assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($faculty->last_login_at)
                                        <small>{{ $faculty->last_login_at->diffForHumans() }}</small>
                                        <br>
                                        <span class="badge bg-success">Recently Active</span>
                                    @else
                                        <span class="badge bg-secondary">Never logged in</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $hasSubjectAssignments = $faculty->facultyAssignments->where('program_id', $program->id)->count() > 0;
                                    @endphp
                                    
                                    @if($hasSubjectAssignments)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('faculty.show', $faculty) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('faculty.assignments', $faculty) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-tasks"></i> Assignments
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5>No Faculty Members Found</h5>
                    <p class="text-muted">No faculty members are currently assigned to your program.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerFacultyModal">
                        <i class="fas fa-user-plus me-1"></i>Register First Faculty User
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Register Faculty Modal -->
<div class="modal fade" id="registerFacultyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Register New Faculty User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="registerFacultyForm">
                    @csrf
                    
                    <!-- Program Info Display -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Program:</strong> {{ $program->name }} <br>
                        <strong>Department:</strong> {{ $program->department->name }}
                    </div>
                    
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="faculty_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-key me-2"></i>Account Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="faculty_email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="faculty_password" name="password" required minlength="8">
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="faculty_password_confirmation" name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Assignment Information -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> The faculty member will be automatically assigned to your program 
                        ({{ $program->name }}) and department ({{ $program->department->name }}) upon registration.
                        You can assign specific subjects after registration is complete.
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitRegistration">
                    <i class="fas fa-user-plus me-1"></i>Register Faculty User
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerFacultyForm');
    const submitBtn = document.getElementById('submitRegistration');
    
    // Handle form submission
    submitBtn.addEventListener('click', function() {
        const formData = new FormData(registerForm);
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        // Disable button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Registering...';
        
        fetch('{{ route("faculty.register") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('registerFacultyModal'));
                modal.hide();
                
                // Reset form
                registerForm.reset();
                
                // Reload page to show new faculty
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[field][0];
                            }
                        }
                    });
                } else {
                    showToast(data.error || 'Registration failed', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred during registration', 'error');
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus me-1"></i>Register Faculty User';
        });
    });
    
    // Clear modal on close
    document.getElementById('registerFacultyModal').addEventListener('hidden.bs.modal', function () {
        registerForm.reset();
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    });
});

function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}
</script>
@endsection