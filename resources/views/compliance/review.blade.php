@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clipboard-check me-2"></i>Review Submissions</h2>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Document Type</label>
                            <select name="document_type" class="form-select">
                                <option value="">All Types</option>
                                <!-- This would be populated with document types -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Faculty</label>
                            <input type="text" name="faculty" class="form-control" placeholder="Search by name..." 
                                   value="{{ request('faculty') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($submissions->count() > 0)
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Document Type</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Files/Links</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="avatar-title bg-primary text-white rounded-circle">
                                                            {{ substr($submission->user->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $submission->user->name }}</div>
                                                        <small class="text-muted">{{ $submission->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $submission->documentType->name }}</div>
                                                <small class="text-muted">{{ ucfirst($submission->documentType->submission_type) }}</small>
                                            </td>
                                            <td>
                                                @if($submission->subject)
                                                    <div class="fw-semibold">{{ $submission->subject->code }}</div>
                                                    <small class="text-muted">{{ Str::limit($submission->subject->name, 30) }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'Not submitted' }}</div>
                                                <small class="text-muted">{{ $submission->submitted_at ? $submission->submitted_at->format('g:i A') : 'Pending' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($submission->complianceDocuments->count() > 0)
                                                        <span class="badge bg-info me-1">
                                                            <i class="fas fa-file me-1"></i>{{ $submission->complianceDocuments->count() }}
                                                        </span>
                                                    @endif
                                                    @if($submission->complianceLinks->count() > 0)
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-link me-1"></i>{{ $submission->complianceLinks->count() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#reviewModal{{ $submission->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($submission->status === 'submitted')
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="quickReview({{ $submission->id }}, 'approve')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="quickReview({{ $submission->id }}, 'reject')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Review Modal -->
                                        <div class="modal fade" id="reviewModal{{ $submission->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            Review Submission: {{ $submission->documentType->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Submission Details -->
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <strong>Faculty:</strong> {{ $submission->user->name }}<br>
                                                                <strong>Email:</strong> {{ $submission->user->email }}<br>
                                                                <strong>Department:</strong> {{ $submission->user->department->name ?? 'N/A' }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Document:</strong> {{ $submission->documentType->name }}<br>
                                                                @if($submission->subject)
                                                                    <strong>Subject:</strong> {{ $submission->subject->code }} - {{ $submission->subject->name }}<br>
                                                                @endif
                                                                <strong>Submitted:</strong> {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y g:i A') : 'Not submitted yet' }}
                                                            </div>
                                                        </div>

                                                        <!-- Files -->
                                                        @if($submission->complianceDocuments->count() > 0)
                                                            <div class="mb-3">
                                                                <h6 class="text-primary">Files</h6>
                                                                <div class="row">
                                                                    @foreach($submission->complianceDocuments as $document)
                                                                        <div class="col-md-6 mb-2">
                                                                            <div class="card card-body">
                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                    <div>
                                                                                        <div class="fw-semibold">{{ $document->filename }}</div>
                                                                                        <small class="text-muted">
                                                                                            {{ number_format($document->file_size / 1024, 1) }} KB
                                                                                        </small>
                                                                                    </div>
                                                                                    <a href="{{ asset('storage/' . $document->file_path) }}" 
                                                                                       class="btn btn-sm btn-outline-primary" 
                                                                                       target="_blank">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Links -->
                                                        @if($submission->complianceLinks->count() > 0)
                                                            <div class="mb-3">
                                                                <h6 class="text-primary">Links</h6>
                                                                @foreach($submission->complianceLinks as $link)
                                                                    <div class="card card-body mb-2">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <div class="fw-semibold">
                                                                                    <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                                                                        {{ $link->title ?: 'Untitled Link' }}
                                                                                    </a>
                                                                                </div>
                                                                                @if($link->description)
                                                                                    <small class="text-muted">{{ $link->description }}</small>
                                                                                @endif
                                                                            </div>
                                                                            <a href="{{ $link->url }}" 
                                                                               class="btn btn-sm btn-outline-primary" 
                                                                               target="_blank">
                                                                                <i class="fas fa-external-link-alt"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <!-- Review Action -->
                                                        @if($submission->status === 'submitted')
                                                            <form action="{{ route('compliance.review-action', $submission) }}" method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label class="form-label">Review Comments</label>
                                                                    <textarea name="comments" class="form-control" rows="3" 
                                                                            placeholder="Add your review comments..."></textarea>
                                                                </div>
                                                                <div class="d-flex gap-2">
                                                                    <button type="submit" name="action" value="approve" 
                                                                            class="btn btn-success">
                                                                        <i class="fas fa-check me-1"></i>Approve
                                                                    </button>
                                                                    <button type="submit" name="action" value="reject" 
                                                                            class="btn btn-danger">
                                                                        <i class="fas fa-times me-1"></i>Reject
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <div class="alert alert-{{ $submission->status === 'approved' ? 'success' : 'warning' }}">
                                                                <strong>Status:</strong> {{ ucfirst($submission->status) }}<br>
                                                                @if($submission->review_comments)
                                                                    <strong>Comments:</strong> {{ $submission->review_comments }}<br>
                                                                @endif
                                                                <strong>Reviewed by:</strong> {{ $submission->reviewer->name ?? 'Unknown' }}<br>
                                                                <strong>Reviewed at:</strong> {{ $submission->reviewed_at ? $submission->reviewed_at->format('M d, Y g:i A') : 'Not reviewed yet' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $submissions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-check fa-5x text-muted mb-3"></i>
                    <h4 class="text-muted">No Submissions Found</h4>
                    <p class="text-muted">There are no submissions matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}
</style>

<script>
function quickReview(submissionId, action) {
    const message = action === 'approve' 
        ? 'Are you sure you want to approve this submission?' 
        : 'Are you sure you want to reject this submission?';
    
    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/compliance/review/${submissionId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        
        form.appendChild(csrfToken);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
