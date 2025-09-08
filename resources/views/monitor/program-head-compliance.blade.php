@extends('layouts.app')

@section('title', 'Monitor Compliances')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-clipboard-check me-2"></i>Monitor Compliances</h2>
            <p class="text-muted">Faculty compliance monitoring for {{ $program->name }}</p>
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
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $facultyCompliance->count() }} Faculty Members</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($facultyCompliance->count() > 0)
        <!-- Faculty Compliance Cards -->
        @foreach($facultyCompliance as $index => $data)
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            {{ $data['faculty']->name }}
                        </h5>
                        <small class="text-muted">{{ $data['faculty']->email }}</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-secondary">Faculty ID: {{ $data['faculty']->id }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Semester-wide Requirements Compliance Table -->
                <h6 class="mb-3">
                    <i class="fas fa-calendar me-2"></i>
                    Semester-wide Requirements Compliance
                    @if($currentSemester)
                        <small class="text-muted">({{ $currentSemester->name }} {{ $currentSemester->academic_year }})</small>
                    @endif
                </h6>
                
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Document Type</th>
                                <th>Status</th>
                                <th>Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['semester_compliances'] as $compliance)
                            <tr>
                                <td>
                                    <strong>{{ $compliance->documentType->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $compliance->documentType->description }}</small>
                                </td>
                                <td>
                                    @if($compliance->self_evaluation_status === 'Complied')
                                        <span class="badge bg-success">Complied</span>
                                    @else
                                        <span class="badge bg-danger">Not Complied</span>
                                    @endif
                                    
                                    @if($compliance->approval_status)
                                        <br>
                                        @if($compliance->approval_status === 'approved')
                                            <small class="text-success">
                                                <i class="fas fa-check-circle"></i> Approved
                                            </small>
                                        @elseif($compliance->approval_status === 'rejected')
                                            <small class="text-danger">
                                                <i class="fas fa-times-circle"></i> Rejected
                                            </small>
                                        @else
                                            <small class="text-warning">
                                                <i class="fas fa-clock"></i> Pending Review
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($compliance->evidence_link)
                                        <a href="{{ $compliance->evidence_link }}" target="_blank" class="text-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>View Link
                                        </a>
                                    @else
                                        <span class="text-muted">No link</span>
                                    @endif
                                </td>
                                <td>
                                    @if($compliance->evidence_link && $compliance->id)
                                        @if($compliance->approval_status !== 'approved')
                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                    data-compliance-id="{{ $compliance->id }}" 
                                                    data-type="semester">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @endif
                                        @if($compliance->approval_status !== 'rejected')
                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                    data-compliance-id="{{ $compliance->id }}" 
                                                    data-type="semester">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted small">No link submitted</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Assigned Subjects -->
                @if($data['assigned_subjects']->count() > 0)
                    <h6 class="mb-3">
                        <i class="fas fa-book me-2"></i>
                        Assigned Subjects ({{ $data['assigned_subjects']->count() }})
                    </h6>
                    
                    <div class="row">
                        @foreach($data['assigned_subjects'] as $subjectData)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ $subjectData['subject']->code }}</h6>
                                    <small class="text-muted">{{ $subjectData['subject']->name }}</small>
                                </div>
                                <div class="card-body p-2">
                                    <button class="btn btn-sm btn-outline-primary w-100 toggle-subject-compliance" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#subject-{{ $data['faculty']->id }}-{{ $subjectData['subject']->id }}"
                                            aria-expanded="false">
                                        <i class="fas fa-eye me-1"></i>View Requirements
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Subject-specific Requirements Compliance Tables (Collapsible) -->
                    @foreach($data['assigned_subjects'] as $subjectData)
                    <div class="collapse mt-3" id="subject-{{ $data['faculty']->id }}-{{ $subjectData['subject']->id }}">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    Subject-specific Requirements Compliance: {{ $subjectData['subject']->code }}
                                </h6>
                                <small>{{ $subjectData['subject']->name }}</small>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Document Type</th>
                                                <th>Status</th>
                                                <th>Link</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subjectData['compliances'] as $compliance)
                                            <tr>
                                                <td>
                                                    <strong>{{ $compliance->documentType->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $compliance->documentType->description }}</small>
                                                </td>
                                                <td>
                                                    @if($compliance->self_evaluation_status === 'Complied')
                                                        <span class="badge bg-success">Complied</span>
                                                    @else
                                                        <span class="badge bg-danger">Not Complied</span>
                                                    @endif
                                                    
                                                    @if($compliance->approval_status)
                                                        <br>
                                                        @if($compliance->approval_status === 'approved')
                                                            <small class="text-success">
                                                                <i class="fas fa-check-circle"></i> Approved
                                                            </small>
                                                        @elseif($compliance->approval_status === 'rejected')
                                                            <small class="text-danger">
                                                                <i class="fas fa-times-circle"></i> Rejected
                                                            </small>
                                                        @else
                                                            <small class="text-warning">
                                                                <i class="fas fa-clock"></i> Pending Review
                                                            </small>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($compliance->evidence_link)
                                                        <a href="{{ $compliance->evidence_link }}" target="_blank" class="text-primary">
                                                            <i class="fas fa-external-link-alt me-1"></i>View Link
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No link</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($compliance->evidence_link && $compliance->id)
                                                        @if($compliance->approval_status !== 'approved')
                                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="subject">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @endif
                                                        @if($compliance->approval_status !== 'rejected')
                                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="subject">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">No link submitted</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No subjects assigned to this faculty member.
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>No Faculty Members Found</h5>
            <p>There are no faculty members assigned to this program.</p>
        </div>
    @endif
</div>

<!-- Comments Modal -->
<div class="modal fade" id="commentsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="commentsForm">
                    <div class="mb-3">
                        <label class="form-label">Review Comments (Optional)</label>
                        <textarea class="form-control" id="reviewComments" rows="3" 
                                  placeholder="Add comments about this compliance submission..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitAction">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentAction = null;
    let currentComplianceId = null;
    let currentType = null;

    // Handle approve buttons
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentAction = 'approve';
            currentComplianceId = this.dataset.complianceId;
            currentType = this.dataset.type;
            
            document.getElementById('commentsModal').querySelector('.modal-title').textContent = 'Approve Compliance';
            document.getElementById('reviewComments').placeholder = 'Add approval comments (optional)...';
            
            const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
            modal.show();
        });
    });

    // Handle reject buttons
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentAction = 'reject';
            currentComplianceId = this.dataset.complianceId;
            currentType = this.dataset.type;
            
            document.getElementById('commentsModal').querySelector('.modal-title').textContent = 'Reject Compliance';
            document.getElementById('reviewComments').placeholder = 'Add rejection reason...';
            
            const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
            modal.show();
        });
    });

    // Handle submit action
    document.getElementById('submitAction').addEventListener('click', function() {
        const comments = document.getElementById('reviewComments').value;
        
        if (!currentAction || !currentComplianceId || !currentType) {
            alert('Invalid action');
            return;
        }

        // Construct URL based on type and action
        const url = `/monitor/${currentType}-compliance/${currentComplianceId}/${currentAction}`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                comments: comments
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('commentsModal'));
                modal.hide();
                
                // Show success message
                showToast(data.message, 'success');
                
                // Refresh page to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.error || 'Error processing request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error processing request', 'error');
        });
    });

    // Clear modal on close
    document.getElementById('commentsModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('reviewComments').value = '';
        currentAction = null;
        currentComplianceId = null;
        currentType = null;
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
