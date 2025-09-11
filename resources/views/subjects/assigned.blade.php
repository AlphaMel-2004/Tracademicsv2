@extends('layouts.app')

@section('title', 'My Assigned Subjects')

@section('content')
<style>
    .semester-requirements-table {
        font-size: 0.9rem;
    }
    
    .semester-requirements-table td {
        vertical-align: middle;
        padding: 12px 8px;
    }
    
    @media (max-width: 768px) {
        .semester-requirements-table {
            font-size: 0.8rem;
        }
        
        .semester-requirements-table td {
            padding: 8px 4px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-book-open me-2"></i>My Assigned Subjects</h2>
            <p class="text-muted">Manage and submit requirements for your assigned subjects</p>
        </div>
    </div>

    <!-- Semester-wide Requirements Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Semester-wide Requirements
                    </h5>
                    <small>Requirements that need to be submitted once per semester</small>
                </div>
                <div class="card-body p-0">
                    @if(isset($semesterCompliances) && $semesterCompliances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 semester-requirements-table" id="semester-requirements-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Document Type</th>
                                        <th style="width: 35%;">Description</th>
                                        <th style="width: 20%;">Evidence</th>
                                        <th style="width: 15%;">Self-Evaluation Status</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($semesterCompliances as $compliance)
                                    <tr data-compliance-id="{{ $compliance->id }}">
                                        <td>
                                            <strong>{{ $compliance->documentType->name }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $compliance->documentType->description }}</small>
                                        </td>
                                        <td>
                                            @if($compliance->evidence_link)
                                                <a href="{{ $compliance->evidence_link }}" target="_blank" class="text-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i>Link
                                                </a>
                                            @else
                                                <span class="text-danger small">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Required
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm compliance-status" data-field="self_evaluation_status">
                                                <option value="Not Complied" {{ $compliance->self_evaluation_status === 'Not Complied' ? 'selected' : '' }}>
                                                    Not Complied
                                                </option>
                                                <option value="Complied" {{ $compliance->self_evaluation_status === 'Complied' ? 'selected' : '' }}>
                                                    Complied
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#linkModal{{ $compliance->id }}">
                                                <i class="fas fa-link"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-file-alt fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No semester requirements found</p>
                            <small>Contact your administrator</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Link Modals for each compliance -->
    @if(isset($semesterCompliances) && $semesterCompliances->count() > 0)
        @foreach($semesterCompliances as $compliance)
        <div class="modal fade" id="linkModal{{ $compliance->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submit Evidence Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form class="link-form" data-compliance-id="{{ $compliance->id }}">
                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>{{ $compliance->documentType->name }}</strong>
                                </label>
                                <p class="text-muted small">{{ $compliance->documentType->description }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Google Drive Link</label>
                                <input 
                                    type="url" 
                                    class="form-control evidence-link-input" 
                                    placeholder="https://drive.google.com/..."
                                    value="{{ $compliance->evidence_link }}"
                                    data-field="evidence_link">
                                <div class="form-text">Please ensure the link is publicly accessible or shared properly</div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary save-link-btn" data-compliance-id="{{ $compliance->id }}">
                            Save Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    <!-- Subjects Grid -->
    <div class="row">
        @forelse($subjects as $subjectData)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $subjectData['subject']->code }}</h5>
                    <span class="badge badge-completion completion-{{ $subjectData['completion_percentage'] >= 80 ? 'high' : ($subjectData['completion_percentage'] >= 60 ? 'medium' : 'low') }}">
                        {{ $subjectData['completion_percentage'] }}%
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $subjectData['subject']->name }}</h6>
                    
                    @if($subjectData['subject']->description)
                    <p class="text-muted small mb-3">{{ $subjectData['subject']->description }}</p>
                    @endif
                    
                    <!-- Assignment Info -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $subjectData['assignment']->semester->name }} {{ $subjectData['assignment']->semester->academic_year }}
                        </small>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Completion Progress</small>
                            <small class="text-muted">{{ $subjectData['completed_requirements'] }}/{{ $subjectData['total_requirements'] }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $subjectData['completion_percentage'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Requirements Summary -->
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Total Requirements</small>
                            <h6 class="text-primary">{{ $subjectData['total_requirements'] }}</h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Completed</small>
                            <h6 class="text-success">{{ $subjectData['completed_requirements'] }}</h6>
                        </div>
                    </div>
                    
                    <!-- Quick Status -->
                    <div class="mt-3">
                        @php
                            $pendingCount = $subjectData['requirements']->where('status', 'pending')->count();
                            $rejectedCount = $subjectData['requirements']->where('status', 'rejected')->count();
                            $notSubmittedCount = $subjectData['requirements']->where('status', 'not_submitted')->count();
                        @endphp
                        
                        @if($pendingCount > 0)
                            <div class="text-warning small">
                                <i class="fas fa-clock me-1"></i>{{ $pendingCount }} pending review
                            </div>
                        @endif
                        
                        @if($rejectedCount > 0)
                            <div class="text-danger small">
                                <i class="fas fa-times-circle me-1"></i>{{ $rejectedCount }} need resubmission
                            </div>
                        @endif
                        
                        @if($notSubmittedCount > 0)
                            <div class="text-muted small">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $notSubmittedCount }} not submitted
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('subjects.assigned.show', $subjectData['subject']) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-tasks me-1"></i>View Requirements
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5>No Subjects Assigned</h5>
                    <p class="text-muted">You don't have any subjects assigned for the current semester yet.</p>
                    <p class="text-muted small">Please contact your Program Head for subject assignments.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.badge-completion {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}

.completion-high {
    background-color: #28a745;
    color: white;
}

.completion-medium {
    background-color: #ffc107;
    color: #212529;
}

.completion-low {
    background-color: #dc3545;
    color: white;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
// Semester Compliance Table Handlers
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save on textarea change (with debounce)
    let timeouts = {};
    
    document.querySelectorAll('.actual-situation').forEach(textarea => {
        textarea.addEventListener('input', function() {
            const complianceId = this.closest('tr').dataset.complianceId;
            const field = this.dataset.field;
            const value = this.value;
            
            // Clear existing timeout
            if (timeouts[complianceId + '_' + field]) {
                clearTimeout(timeouts[complianceId + '_' + field]);
            }
            
            // Set new timeout for auto-save
            timeouts[complianceId + '_' + field] = setTimeout(() => {
                updateCompliance(complianceId, field, value);
            }, 1000); // Auto-save after 1 second of no typing
        });
    });

    // Auto-save on select change
    document.querySelectorAll('.compliance-status').forEach(select => {
        select.addEventListener('change', function() {
            const complianceId = this.closest('tr').dataset.complianceId;
            const field = this.dataset.field;
            const value = this.value;
            
            updateCompliance(complianceId, field, value);
        });
    });

    // Save link button handlers
    document.querySelectorAll('.save-link-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const complianceId = this.dataset.complianceId;
            const modal = document.getElementById('linkModal' + complianceId);
            const linkInput = modal.querySelector('.evidence-link-input');
            const link = linkInput.value.trim();
            
            // Update compliance with new link
            updateCompliance(complianceId, 'evidence_link', link, () => {
                // Close modal on success
                const bsModal = bootstrap.Modal.getInstance(modal);
                bsModal.hide();
                
                // Show success message
                showToast('Evidence link saved successfully', 'success');
                
                // Log for debugging
                console.log('Link saved successfully, compliance ID:', complianceId);
                
                // Refresh the page to ensure data persistence is visible
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        });
    });
});

function updateCompliance(complianceId, field, value, successCallback = null) {
    console.log('Updating compliance:', { complianceId, field, value });
    
    // Prepare the data to send
    const data = {
        _method: 'PUT'
    };
    
    // Only send the field that's being updated
    data[field] = value || '';

    console.log('Sending data:', data);

    fetch(`/faculty-compliance/${complianceId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(responseData => {
        console.log('Server response:', responseData);
        
        if (responseData.success) {
            // Show success feedback
            showToast('Changes saved successfully', 'success');
            
            // Update the UI with the latest data
            if (responseData.data && field === 'evidence_link') {
                console.log('Updating evidence display with:', responseData.data.evidence_link);
                updateEvidenceDisplay(complianceId, responseData.data.evidence_link);
                
                // Update status dropdown if it was auto-updated
                if (responseData.data.self_evaluation_status) {
                    const statusSelect = document.querySelector(`tr[data-compliance-id="${complianceId}"] .compliance-status`);
                    if (statusSelect) {
                        statusSelect.value = responseData.data.self_evaluation_status;
                    }
                }
            }
            
            if (successCallback) {
                successCallback();
            }
        } else {
            console.error('Server error:', responseData);
            showToast(responseData.message || 'Error saving changes', 'error');
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        showToast('Error saving changes: ' + error.message, 'error');
    });
}

function updateEvidenceDisplay(complianceId, link) {
    const row = document.querySelector(`tr[data-compliance-id="${complianceId}"]`);
    const evidenceCell = row.children[2]; // Evidence column (now index 2 after removing actual_situation)
    
    if (link) {
        evidenceCell.innerHTML = `
            <a href="${link}" target="_blank" class="text-primary">
                <i class="fas fa-external-link-alt me-1"></i>Link
            </a>
        `;
    } else {
        evidenceCell.innerHTML = `
            <span class="text-danger small">
                <i class="fas fa-exclamation-triangle me-1"></i>Required
            </span>
        `;
    }
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show toast
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove toast after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}
</script>
@endsection
