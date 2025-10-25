@extends('layouts.app')

@section('title', 'Subject Requirements')

@section('content')
<style>
    .subject-requirements-table {
        font-size: 0.9rem;
    }
    
    .subject-requirements-table td {
        vertical-align: middle;
        padding: 12px 8px;
    }
    
    @media (max-width: 768px) {
        .subject-requirements-table {
            font-size: 0.8rem;
        }
        
        .subject-requirements-table td {
            padding: 8px 4px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('subjects.assigned') }}">My Subjects</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $subject->code }}</li>
                </ol>
            </nav>
            <h2><i class="fas fa-tasks me-2"></i>{{ $subject->code }} - Requirements</h2>
            <p class="text-muted">{{ $subject->name }}</p>
        </div>
    </div>

    <!-- Subject Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $subject->name }}</h4>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $assignment->semester->name }} {{ $assignment->semester->academic_year }}
                            </p>
                            @if($subject->description)
                            <p class="mb-0 mt-2 opacity-75">{{ $subject->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $stats['completion_percentage'] }}% Complete</h3>
                            <small>{{ $stats['completed_requirements'] }}/{{ $stats['total_requirements'] }} Requirements</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['completed_requirements'] }}</h4>
                    <small>Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['pending_requirements'] }}</h4>
                    <small>Pending Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['needs_revision_requirements'] }}</h4>
                    <small>Needs Revision</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['not_submitted_requirements'] }}</h4>
                    <small>Not Submitted</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Requirements Table -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-book me-2"></i>
                Subject-specific Requirements - {{ $subject->code }}
            </h5>
            <small>Requirements that need to be submitted for this specific subject</small>
        </div>
        <div class="card-body p-0">
            @if($requirements && $requirements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 subject-requirements-table" id="subject-requirements-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Document Type</th>
                                <th>Description</th>
                                <th>Evidence</th>
                                <th>Document Status</th>
                                <th>Self-Evaluation Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requirements as $requirement)
                                <tr data-compliance-id="{{ $requirement['compliance']->id }}">
                                    <td>
                                        <strong>{{ $requirement['document_type']->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $requirement['document_type']->description }}</small>
                                    </td>
                                    <td>
                                        @if($requirement['compliance']->evidence_link)
                                            <a href="{{ $requirement['compliance']->evidence_link }}" target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>Link
                                            </a>
                                        @else
                                            <span class="text-danger small">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Required
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $approvalStatus = $requirement['compliance']->approval_status ?? 'draft';
                                            $statusMap = [
                                                'draft' => ['label' => 'Draft', 'class' => 'secondary', 'icon' => 'file'],
                                                'submitted' => ['label' => 'Submitted', 'class' => 'info', 'icon' => 'paper-plane'],
                                                'pending' => ['label' => 'Pending Review', 'class' => 'warning', 'icon' => 'clock'],
                                                'approved' => ['label' => 'Approved', 'class' => 'success', 'icon' => 'check-circle'],
                                                'needs_revision' => ['label' => 'Needs Revision', 'class' => 'danger', 'icon' => 'exclamation-circle'],
                                            ];
                                            $statusDisplay = $statusMap[$approvalStatus] ?? ['label' => ucfirst($approvalStatus), 'class' => 'secondary', 'icon' => 'question-circle'];

                                            $stageLabels = [
                                                'pending' => ['label' => 'Pending', 'class' => 'text-muted', 'icon' => 'clock'],
                                                'approved' => ['label' => 'Approved', 'class' => 'text-success', 'icon' => 'check-circle'],
                                                'needs_revision' => ['label' => 'Needs Revision', 'class' => 'text-warning', 'icon' => 'exclamation-circle'],
                                            ];

                                            $programHeadStatus = $requirement['compliance']->program_head_approval_status ?? 'pending';
                                            $deanStatus = $requirement['compliance']->dean_approval_status ?? 'pending';
                                            $phDisplay = $stageLabels[$programHeadStatus] ?? $stageLabels['pending'];
                                            $deanDisplay = $stageLabels[$deanStatus] ?? $stageLabels['pending'];
                                        @endphp
                                        <span class="badge bg-{{ $statusDisplay['class'] }}">
                                            <i class="fas fa-{{ $statusDisplay['icon'] }} me-1"></i>{{ $statusDisplay['label'] }}
                                        </span>
                                        <div class="small mt-2">
                                            <div class="{{ $phDisplay['class'] }}">
                                                <i class="fas fa-user-tie me-1"></i>PH: {{ $phDisplay['label'] }}
                                            </div>
                                            <div class="{{ $deanDisplay['class'] }}">
                                                <i class="fas fa-user-graduate me-1"></i>Dean: {{ $deanDisplay['label'] }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm compliance-status" data-field="self_evaluation_status">
                                            <option value="Not Complied" {{ $requirement['compliance']->self_evaluation_status === 'Not Complied' ? 'selected' : '' }}>
                                                Not Complied
                                            </option>
                                            <option value="Complied" {{ $requirement['compliance']->self_evaluation_status === 'Complied' ? 'selected' : '' }}>
                                                Complied
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#linkModal{{ $requirement['compliance']->id }}">
                                            <i class="fas fa-link"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="mb-0">No subject requirements found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Link Modals for each requirement -->
@if($requirements && $requirements->count() > 0)
    @foreach($requirements as $requirement)
    <div class="modal fade" id="linkModal{{ $requirement['compliance']->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Evidence Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="link-form" data-compliance-id="{{ $requirement['compliance']->id }}">
                        <div class="mb-3">
                            <label class="form-label">
                                <strong>{{ $requirement['document_type']->name }}</strong>
                            </label>
                            <p class="text-muted small">{{ $requirement['document_type']->description }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Google Drive Link</label>
                            <input 
                                type="url" 
                                class="form-control evidence-link-input" 
                                placeholder="https://drive.google.com/..."
                                value="{{ $requirement['compliance']->evidence_link }}"
                                data-field="evidence_link">
                            <div class="form-text">Please ensure the link is publicly accessible or shared properly</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary save-link-btn" data-compliance-id="{{ $requirement['compliance']->id }}">
                        Save Link
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif

<script>
// Subject Requirements Table Handlers
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
                updateSubjectCompliance(complianceId, field, value);
            }, 1000); // Auto-save after 1 second of no typing
        });
    });

    // Auto-save on select change
    document.querySelectorAll('.compliance-status').forEach(select => {
        select.addEventListener('change', function() {
            const complianceId = this.closest('tr').dataset.complianceId;
            const field = this.dataset.field;
            const value = this.value;
            
            updateSubjectCompliance(complianceId, field, value);
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
            updateSubjectCompliance(complianceId, 'evidence_link', link, () => {
                // Close modal on success
                const bsModal = bootstrap.Modal.getInstance(modal);
                bsModal.hide();
                
                // Show success message
                showToast('Evidence link saved successfully', 'success');
            });
        });
    });
});

function updateSubjectCompliance(complianceId, field, value, successCallback = null) {
    console.log('Updating subject compliance:', { complianceId, field, value });
    
    // Prepare the data to send
    const data = {
        _method: 'PUT'
    };
    
    // Only send the field that's being updated
    data[field] = value || '';

    fetch(`/subject-compliance/${complianceId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
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
            showToast('Error saving changes', 'error');
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        showToast('Error saving changes', 'error');
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
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
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
