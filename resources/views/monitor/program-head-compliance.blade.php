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
                            <tr data-compliance-row="{{ $compliance->id }}" data-compliance-type="semester">
                                <td>
                                    <strong>{{ $compliance->documentType->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $compliance->documentType->description }}</small>
                                </td>
                                <td class="status-cell">
                                    <div class="status-wrapper">
                                        @if($compliance->self_evaluation_status === 'Complied')
                                            <span class="badge bg-success">Complied</span>
                                        @else
                                            <span class="badge bg-danger">Not Complied</span>
                                        @endif
                                        
                                        @if($compliance->program_head_approval_status || $compliance->dean_approval_status || $compliance->approval_status)
                                            <br>
                                            <!-- Program Head Approval Status -->
                                            @if($compliance->program_head_approval_status)
                                                <small class="d-block mt-1">
                                                    @if($compliance->program_head_approval_status === 'approved')
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle"></i> PH: Approved
                                                        </span>
                                                    @elseif($compliance->program_head_approval_status === 'needs_revision')
                                                        <span class="text-warning">
                                                            <i class="fas fa-edit"></i> PH: Needs Revision
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-clock"></i> PH: Pending
                                                        </span>
                                                    @endif
                                                </small>
                                            @endif
                                            
                                            <!-- Dean Approval Status -->
                                            @if($compliance->dean_approval_status)
                                                <small class="d-block">
                                                    @if($compliance->dean_approval_status === 'approved')
                                                        <span class="text-success">
                                                            <i class="fas fa-check-double"></i> Dean: Approved
                                                        </span>
                                                    @elseif($compliance->dean_approval_status === 'needs_revision')
                                                        <span class="text-warning">
                                                            <i class="fas fa-edit"></i> Dean: Needs Revision
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-clock"></i> Dean: Pending
                                                        </span>
                                                    @endif
                                                </small>
                                            @endif
                                            
                                            <!-- Overall Status -->
                                            @if($compliance->approval_status === 'approved')
                                                <span class="badge bg-success mt-1">
                                                    <i class="fas fa-trophy"></i> FULLY APPROVED
                                                </span>
                                            @elseif($compliance->approval_status === 'needs_revision')
                                                <span class="badge bg-warning mt-1">
                                                    <i class="fas fa-edit"></i> NEEDS REVISION
                                                </span>
                                            @else
                                                @if($compliance->program_head_approval_status === 'approved' && !$compliance->dean_approval_status)
                                                    <span class="badge bg-info mt-1">
                                                        <i class="fas fa-arrow-up"></i> AWAITING DEAN
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary mt-1">
                                                        <i class="fas fa-clock"></i> PENDING REVIEW
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="badge bg-secondary mt-2">
                                                <i class="fas fa-clock"></i> Not Reviewed
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="link-cell">
                                    <div class="link-wrapper">
                                        @if($compliance->evidence_link)
                                            <a href="{{ $compliance->evidence_link }}" target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>View Link
                                            </a>
                                        @else
                                            <span class="text-muted">No link</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <div class="actions-wrapper">
                                        @if($compliance->evidence_link && $compliance->id)
                                            @auth
                                                @if(Auth::user()->role->name !== 'VPAA')
                                                    <!-- Program Head Actions -->
                                                    @if(Auth::user()->role->name === 'Program Head')
                                                        @if($compliance->program_head_approval_status !== 'approved')
                                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="semester">
                                                                <i class="fas fa-check"></i> PH Approve
                                                            </button>
                                                        @endif
                                                        @if($compliance->program_head_approval_status !== 'needs_revision')
                                                            <button class="btn btn-sm btn-warning needs-revision-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="semester">
                                                                <i class="fas fa-edit"></i> PH Revision
                                                            </button>
                                                        @endif
                                                    @endif
                                                    
                                                    <!-- Dean Actions -->
                                                    @if(Auth::user()->role->name === 'Dean')
                                                        @if($compliance->dean_approval_status !== 'approved')
                                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="semester"
                                                                    @if($compliance->program_head_approval_status !== 'approved') disabled title="Awaiting Program Head approval" @endif>
                                                                <i class="fas fa-check"></i> Dean Approve
                                                            </button>
                                                        @endif
                                                        @if($compliance->dean_approval_status !== 'needs_revision')
                                                            <button class="btn btn-sm btn-warning needs-revision-btn" 
                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                    data-type="semester">
                                                                <i class="fas fa-edit"></i> Dean Revision
                                                            </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-eye"></i> View Only
                                                    </span>
                                                @endif
                                            @endauth
                                        @else
                                            <span class="text-muted small">No link submitted</span>
                                        @endif
                                    </div>
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
                                            <tr data-compliance-row="{{ $compliance->id }}" data-compliance-type="subject">
                                                <td>
                                                    <strong>{{ $compliance->documentType->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $compliance->documentType->description }}</small>
                                                </td>
                                                <td class="status-cell">
                                                    <div class="status-wrapper">
                                                        @if($compliance->self_evaluation_status === 'Complied')
                                                            <span class="badge bg-success">Complied</span>
                                                        @else
                                                            <span class="badge bg-danger">Not Complied</span>
                                                        @endif
                                                        
                                                        @if($compliance->program_head_approval_status || $compliance->dean_approval_status || $compliance->approval_status)
                                                            <br>
                                                            <!-- Program Head Approval Status -->
                                                            @if($compliance->program_head_approval_status)
                                                                <small class="d-block mt-1">
                                                                    @if($compliance->program_head_approval_status === 'approved')
                                                                        <span class="text-success">
                                                                            <i class="fas fa-check-circle"></i> PH: Approved
                                                                        </span>
                                                                    @elseif($compliance->program_head_approval_status === 'needs_revision')
                                                                        <span class="text-warning">
                                                                            <i class="fas fa-edit"></i> PH: Needs Revision
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">
                                                                            <i class="fas fa-clock"></i> PH: Pending
                                                                        </span>
                                                                    @endif
                                                                </small>
                                                            @endif
                                                            
                                                            <!-- Dean Approval Status -->
                                                            @if($compliance->dean_approval_status)
                                                                <small class="d-block">
                                                                    @if($compliance->dean_approval_status === 'approved')
                                                                        <span class="text-success">
                                                                            <i class="fas fa-check-double"></i> Dean: Approved
                                                                        </span>
                                                                    @elseif($compliance->dean_approval_status === 'needs_revision')
                                                                        <span class="text-warning">
                                                                            <i class="fas fa-edit"></i> Dean: Needs Revision
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">
                                                                            <i class="fas fa-clock"></i> Dean: Pending
                                                                        </span>
                                                                    @endif
                                                                </small>
                                                            @endif
                                                            
                                                            <!-- Overall Status -->
                                                            @if($compliance->approval_status === 'approved')
                                                                <span class="badge bg-success mt-1">
                                                                    <i class="fas fa-trophy"></i> FULLY APPROVED
                                                                </span>
                                                            @elseif($compliance->approval_status === 'needs_revision')
                                                                <span class="badge bg-warning mt-1">
                                                                    <i class="fas fa-edit"></i> NEEDS REVISION
                                                                </span>
                                                            @else
                                                                @if($compliance->program_head_approval_status === 'approved' && !$compliance->dean_approval_status)
                                                                    <span class="badge bg-info mt-1">
                                                                        <i class="fas fa-arrow-up"></i> AWAITING DEAN
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary mt-1">
                                                                        <i class="fas fa-clock"></i> PENDING REVIEW
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary mt-2">
                                                                <i class="fas fa-clock"></i> Not Reviewed
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="link-cell">
                                                    <div class="link-wrapper">
                                                        @if($compliance->evidence_link)
                                                            <a href="{{ $compliance->evidence_link }}" target="_blank" class="text-primary">
                                                                <i class="fas fa-external-link-alt me-1"></i>View Link
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No link</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="actions-cell">
                                                    <div class="actions-wrapper">
                                                        @if($compliance->evidence_link && $compliance->id)
                                                            @auth
                                                                @if(Auth::user()->role->name !== 'VPAA')
                                                                    <!-- Program Head Actions -->
                                                                    @if(Auth::user()->role->name === 'Program Head')
                                                                        @if($compliance->program_head_approval_status !== 'approved')
                                                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                                    data-type="subject">
                                                                                <i class="fas fa-check"></i> PH Approve
                                                                            </button>
                                                                        @endif
                                                                        @if($compliance->program_head_approval_status !== 'needs_revision')
                                                                            <button class="btn btn-sm btn-warning needs-revision-btn" 
                                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                                    data-type="subject">
                                                                                <i class="fas fa-edit"></i> PH Revision
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                    
                                                                    <!-- Dean Actions -->
                                                                    @if(Auth::user()->role->name === 'Dean')
                                                                        @if($compliance->dean_approval_status !== 'approved')
                                                                            <button class="btn btn-sm btn-success me-1 approve-btn" 
                                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                                    data-type="subject"
                                                                                    @if($compliance->program_head_approval_status !== 'approved') disabled title="Awaiting Program Head approval" @endif>
                                                                                <i class="fas fa-check"></i> Dean Approve
                                                                            </button>
                                                                        @endif
                                                                        @if($compliance->dean_approval_status !== 'needs_revision')
                                                                            <button class="btn btn-sm btn-warning needs-revision-btn" 
                                                                                    data-compliance-id="{{ $compliance->id }}" 
                                                                                    data-type="subject">
                                                                                <i class="fas fa-edit"></i> Dean Revision
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-info">
                                                                        <i class="fas fa-eye"></i> View Only
                                                                    </span>
                                                                @endif
                                                            @endauth
                                                        @else
                                                            <span class="text-muted small">No link submitted</span>
                                                        @endif
                                                    </div>
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
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('commentsModal');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    const commentsField = document.getElementById('reviewComments');
    const modalTitle = modalEl.querySelector('.modal-title');
    const submitActionBtn = document.getElementById('submitAction');
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const userRole = document.body.dataset.userRole || '';
    const roleLabel = ['Dean', 'Program Head'].includes(userRole) ? `${userRole} ` : '';

    let currentAction = null;
    let currentComplianceId = null;
    let currentType = null;

    document.addEventListener('click', event => {
        const approveBtn = event.target.closest('.approve-btn');
        if (approveBtn) {
            currentAction = 'approve';
            currentComplianceId = approveBtn.dataset.complianceId;
            currentType = approveBtn.dataset.type;
            modalTitle.textContent = roleLabel ? `${roleLabel}Approve Compliance` : 'Approve Compliance';
            commentsField.placeholder = roleLabel ? `${roleLabel}Add approval comments (optional)...` : 'Add approval comments (optional)...';
            modalInstance.show();
            return;
        }

        const revisionBtn = event.target.closest('.needs-revision-btn');
        if (revisionBtn) {
            currentAction = 'reject';
            currentComplianceId = revisionBtn.dataset.complianceId;
            currentType = revisionBtn.dataset.type;
            modalTitle.textContent = roleLabel ? `${roleLabel}Mark for Revision` : 'Mark for Revision';
            commentsField.placeholder = roleLabel ? `${roleLabel}Add revision comments...` : 'Add revision comments...';
            modalInstance.show();
        }
    });

    submitActionBtn.addEventListener('click', () => {
        const comments = commentsField.value.trim();

        if (!currentAction || !currentComplianceId || !currentType) {
            showToast('Invalid action. Please try again.', 'error');
            return;
        }

        if (currentAction === 'reject' && !comments) {
            showToast('Please add revision comments before submitting.', 'warning');
            return;
        }

        if (!csrfTokenMeta) {
            showToast('CSRF token not found. Please refresh the page.', 'error');
            return;
        }

        const url = currentAction === 'approve'
            ? `/monitor/${currentType}-compliance/${currentComplianceId}/approve`
            : `/monitor/${currentType}-compliance/${currentComplianceId}/needs-revision`;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comments })
        })
            .then(handleFetchResponse)
            .then(data => {
                if (data?.success) {
                    modalInstance.hide();
                    commentsField.value = '';
                    const toastType = currentAction === 'approve' ? 'success' : 'warning';
                    showToast(data.message, toastType);
                    if (data.compliance) {
                        updateComplianceRow(currentComplianceId, currentType, data.compliance);
                    }
                } else {
                    showToast(data?.error || 'Error processing request.', 'error');
                }
            })
            .catch(error => {
                console.error('Compliance action error:', error);
                showToast(error.message || 'Error processing request.', 'error');
            });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        commentsField.value = '';
        currentAction = null;
        currentComplianceId = null;
        currentType = null;
    });
});

function handleFetchResponse(response) {
    if (response.ok) {
        return response.json();
    }

    if (response.status === 422) {
        return response.json().then(errorData => {
            const messages = errorData.errors ? Object.values(errorData.errors).flat() : [errorData.message];
            throw new Error(messages.filter(Boolean).join(', ') || 'Validation error occurred.');
        });
    }

    if (response.status === 403) {
        throw new Error('Access denied. You may not have permission to perform this action.');
    }

    if (response.status === 404) {
        throw new Error('Compliance record not found.');
    }

    if (response.status === 500) {
        throw new Error('Server error occurred. Please try again or contact support.');
    }

    const contentType = response.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
        return response.json().then(errorData => {
            throw new Error(errorData.message || errorData.error || `Server error (${response.status}).`);
        });
    }

    return response.text().then(text => {
        throw new Error(text || `Server error (${response.status}).`);
    });
}

function updateComplianceRow(complianceId, type, compliance) {
    const row = document.querySelector(`tr[data-compliance-row="${complianceId}"][data-compliance-type="${type}"]`);
    if (!row) {
        console.warn('Compliance row not found for update', { complianceId, type });
        return;
    }

    const statusCell = row.querySelector('.status-cell');
    if (statusCell) {
        statusCell.innerHTML = buildStatusMarkup(compliance);
    }

    const actionsCell = row.querySelector('.actions-cell');
    if (actionsCell) {
        actionsCell.innerHTML = buildActionsMarkup(compliance, type);
    }
}

function buildStatusMarkup(compliance) {
    const isComplied = (compliance.self_evaluation_status || '').toLowerCase() === 'complied';
    const baseBadge = isComplied
        ? '<span class="badge bg-success">Complied</span>'
        : '<span class="badge bg-danger">Not Complied</span>';

    const hasApprovalInfo = Boolean(
        (compliance.program_head_approval_status && compliance.program_head_approval_status !== '') ||
        (compliance.dean_approval_status && compliance.dean_approval_status !== '') ||
        (compliance.approval_status && compliance.approval_status !== '')
    );

    if (!hasApprovalInfo) {
        return `<div class="status-wrapper">${baseBadge}<span class="badge bg-secondary mt-2"><i class="fas fa-clock"></i> Not Reviewed</span></div>`;
    }

    const fragments = [
        baseBadge,
        '<br>'
    ];

    if (compliance.program_head_approval_status) {
        fragments.push(renderRoleStatus('PH', compliance.program_head_approval_status));
    }

    if (compliance.dean_approval_status) {
        fragments.push(renderRoleStatus('Dean', compliance.dean_approval_status));
    }

    fragments.push(renderOverallStatus(compliance));

    return `<div class="status-wrapper">${fragments.join('')}</div>`;
}

function renderRoleStatus(role, status) {
    const normalized = (status || '').toLowerCase();
    const isProgramHead = role === 'PH';
    const marginClass = isProgramHead ? 'mt-1' : '';
    let colorClass = 'text-muted';
    let icon = 'fa-clock';
    let text = 'Pending';

    if (normalized === 'approved') {
        colorClass = 'text-success';
        icon = isProgramHead ? 'fa-check-circle' : 'fa-check-double';
        text = 'Approved';
    } else if (normalized === 'needs_revision') {
        colorClass = 'text-warning';
        icon = 'fa-edit';
        text = 'Needs Revision';
    }

    const roleLabel = isProgramHead ? 'PH' : 'Dean';

    return `<small class="d-block ${marginClass}"><span class="${colorClass}"><i class="fas ${icon}"></i> ${roleLabel}: ${text}</span></small>`;
}

function renderOverallStatus(compliance) {
    const overallStatus = (compliance.approval_status || '').toLowerCase();

    if (overallStatus === 'approved') {
        return '<span class="badge bg-success mt-1"><i class="fas fa-trophy"></i> FULLY APPROVED</span>';
    }

    if (overallStatus === 'needs_revision') {
        return '<span class="badge bg-warning mt-1"><i class="fas fa-edit"></i> NEEDS REVISION</span>';
    }

    const programHeadApproved = (compliance.program_head_approval_status || '').toLowerCase() === 'approved';
    const deanStatusPresent = Boolean(compliance.dean_approval_status);

    if (programHeadApproved && !deanStatusPresent) {
        return '<span class="badge bg-info mt-1"><i class="fas fa-arrow-up"></i> AWAITING DEAN</span>';
    }

    return '<span class="badge bg-secondary mt-1"><i class="fas fa-clock"></i> PENDING REVIEW</span>';
}

function buildActionsMarkup(compliance, type) {
    if (!compliance.evidence_link || !compliance.id) {
        return '<div class="actions-wrapper"><span class="text-muted small">No link submitted</span></div>';
    }

    const userRole = document.body.dataset.userRole || '';
    if (userRole === 'VPAA') {
        return '<div class="actions-wrapper"><span class="badge bg-info"><i class="fas fa-eye"></i> View Only</span></div>';
    }

    const buttons = [];

    if (userRole === 'Program Head') {
        if (compliance.program_head_approval_status !== 'approved') {
            buttons.push(`<button class="btn btn-sm btn-success me-1 approve-btn" data-compliance-id="${compliance.id}" data-type="${type}"><i class="fas fa-check"></i> PH Approve</button>`);
        }
        if (compliance.program_head_approval_status !== 'needs_revision') {
            buttons.push(`<button class="btn btn-sm btn-warning needs-revision-btn" data-compliance-id="${compliance.id}" data-type="${type}"><i class="fas fa-edit"></i> PH Revision</button>`);
        }
    } else if (userRole === 'Dean') {
        if (compliance.dean_approval_status !== 'approved') {
            const disabledAttr = (compliance.program_head_approval_status || '').toLowerCase() === 'approved'
                ? ''
                : ' disabled title="Awaiting Program Head approval"';
            buttons.push(`<button class="btn btn-sm btn-success me-1 approve-btn" data-compliance-id="${compliance.id}" data-type="${type}"${disabledAttr}><i class="fas fa-check"></i> Dean Approve</button>`);
        }
        if (compliance.dean_approval_status !== 'needs_revision') {
            buttons.push(`<button class="btn btn-sm btn-warning needs-revision-btn" data-compliance-id="${compliance.id}" data-type="${type}"><i class="fas fa-edit"></i> Dean Revision</button>`);
        }
    } else {
        return '<div class="actions-wrapper"><span class="badge bg-info"><i class="fas fa-eye"></i> View Only</span></div>';
    }

    if (!buttons.length) {
        return '<div class="actions-wrapper"><span class="text-muted small">No actions available</span></div>';
    }

    return `<div class="actions-wrapper">${buttons.join('')}</div>`;
}

function showToast(message, type = 'info') {
    const variants = {
        success: { wrapper: 'bg-success text-white', close: 'btn-close-white' },
        warning: { wrapper: 'bg-warning text-dark', close: '' },
        info: { wrapper: 'bg-info text-dark', close: '' },
        error: { wrapper: 'bg-danger text-white', close: 'btn-close-white' }
    };

    const variant = variants[type] || variants.info;
    const closeClass = variant.close ? `btn-close ${variant.close}` : 'btn-close';

    const toastHtml = `
        <div class="toast align-items-center ${variant.wrapper} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="${closeClass} me-2 m-auto" data-bs-dismiss="toast"></button>
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
