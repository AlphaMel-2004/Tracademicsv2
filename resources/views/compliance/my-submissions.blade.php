@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt me-2"></i>My Submissions</h2>
                <div>
                    @if($activeSemester)
                        <span class="badge bg-info me-2">{{ $activeSemester->name }}</span>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            @if($submissions->count() > 0)
                <div class="row">
                    @foreach($submissions as $submission)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $submission->documentType->name }}</h6>
                                    <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <!-- Submission Details -->
                                    <div class="mb-3">
                                        @if($submission->subject)
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-book me-1"></i>
                                                {{ $submission->subject->code }} - {{ $submission->subject->name }}
                                            </p>
                                        @endif
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            Submitted: {{ $submission->submitted_at->format('M d, Y g:i A') }}
                                        </p>
                                        @if($submission->reviewed_at)
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-user-check me-1"></i>
                                                Reviewed: {{ $submission->reviewed_at->format('M d, Y g:i A') }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Files -->
                                    @if($submission->complianceDocuments->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-primary">
                                                <i class="fas fa-file me-1"></i>Files ({{ $submission->complianceDocuments->count() }})
                                            </h6>
                                            @foreach($submission->complianceDocuments as $document)
                                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-1">
                                                    <div class="flex-grow-1">
                                                        <small class="text-truncate d-block">{{ $document->filename }}</small>
                                                        <small class="text-muted">{{ number_format($document->file_size / 1024, 1) }} KB</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ asset('storage/' . $document->file_path) }}" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           target="_blank" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($submission->status !== 'approved')
                                                            <form action="{{ route('compliance.delete-file', $document) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                        title="Delete" 
                                                                        onclick="return confirm('Are you sure you want to delete this file?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Links -->
                                    @if($submission->complianceLinks->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-primary">
                                                <i class="fas fa-link me-1"></i>Links ({{ $submission->complianceLinks->count() }})
                                            </h6>
                                            @foreach($submission->complianceLinks as $link)
                                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-1">
                                                    <div class="flex-grow-1">
                                                        <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                                            <small class="d-block">{{ $link->title ?: 'Untitled Link' }}</small>
                                                        </a>
                                                        @if($link->description)
                                                            <small class="text-muted">{{ Str::limit($link->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                    @if($submission->status !== 'approved')
                                                        <form action="{{ route('compliance.delete-link', $link) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                    title="Delete" 
                                                                    onclick="return confirm('Are you sure you want to delete this link?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Review Comments -->
                                    @if($submission->review_comments)
                                        <div class="alert alert-{{ $submission->status === 'approved' ? 'success' : 'warning' }} alert-sm">
                                            <strong>Review Comments:</strong><br>
                                            {{ $submission->review_comments }}
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    @if($submission->status === 'rejected' || $submission->status === 'submitted')
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateModal{{ $submission->id }}">
                                            <i class="fas fa-edit me-1"></i>
                                            {{ $submission->status === 'rejected' ? 'Resubmit' : 'Update' }}
                                        </button>
                                    @endif
                                    
                                    @if($submission->status === 'approved')
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Update Modal -->
                        <div class="modal fade" id="updateModal{{ $submission->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Update: {{ $submission->documentType->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('compliance.update', $submission) }}" 
                                          method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <!-- Additional Files -->
                                            <div class="mb-3">
                                                <label class="form-label">Add More Files</label>
                                                <input type="file" name="files[]" class="form-control" multiple 
                                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <small class="form-text text-muted">
                                                    Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB each)
                                                </small>
                                            </div>

                                            <!-- Additional Links -->
                                            <div class="mb-3">
                                                <label class="form-label">Add More Links</label>
                                                <div class="border rounded p-3">
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <input type="url" name="links[0][url]" class="form-control" 
                                                                   placeholder="https://example.com">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="links[0][title]" class="form-control" 
                                                                   placeholder="Link title">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <textarea name="links[0][description]" class="form-control" rows="2" 
                                                                    placeholder="Link description (optional)"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
                    <h4 class="text-muted">No Submissions Yet</h4>
                    <p class="text-muted">You haven't submitted any documents for the current semester.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Submit Your First Document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.card-footer {
    background-color: rgba(0,0,0,.03);
    border-top: 1px solid rgba(0,0,0,.125);
}

.text-truncate {
    max-width: 150px;
}
</style>
@endsection
