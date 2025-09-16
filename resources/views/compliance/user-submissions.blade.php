@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">{{ $facultyUser->name }} - Compliance Submissions</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @if(auth()->user()->role->name === 'Program Head')
                                <li class="breadcrumb-item"><a href="{{ route('monitor.compliance') }}">Monitor Compliance</a></li>
                            @endif
                            <li class="breadcrumb-item active">Faculty Submissions</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ auth()->user()->role->name === 'Program Head' ? route('monitor.compliance') : route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>

            <!-- Faculty Info Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-3">Faculty Information</h5>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p><strong>Name:</strong> {{ $facultyUser->name }}</p>
                                            <p><strong>Email:</strong> {{ $facultyUser->email }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p><strong>Department:</strong> {{ $facultyUser->department->name ?? 'N/A' }}</p>
                                            <p><strong>Faculty Type:</strong> {{ ucfirst($facultyUser->faculty_type ?? 'N/A') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6>Submission Statistics</h6>
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <span class="stat-value">{{ $submissions->count() }}</span>
                                            <span class="stat-label">Total</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value text-success">{{ $submissions->where('status', 'approved')->count() }}</span>
                                            <span class="stat-label">Approved</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value text-warning">{{ $submissions->where('status', 'pending')->count() }}</span>
                                            <span class="stat-label">Pending</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value text-warning">{{ $submissions->where('status', 'needs_revision')->count() }}</span>
                                            <span class="stat-label">Needs Revision</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Compliance Submissions</h5>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Subject</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Links</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                    <tr>
                                        <td>
                                            <strong>{{ $submission->documentType->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $submission->documentType->description }}</small>
                                        </td>
                                        <td>
                                            {{ $submission->subject->code ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $submission->subject->name ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $submission->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $submission->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($submission->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($submission->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-warning">Needs Revision</span>
                                            @endif
                                            @if($submission->reviewed_at)
                                                <br>
                                                <small class="text-muted">{{ $submission->reviewed_at->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->complianceDocuments->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($submission->complianceDocuments as $document)
                                                        <a href="{{ Storage::url($document->file_path) }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="{{ $document->original_name }}">
                                                            <i class="fas fa-file"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No files</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->complianceLinks->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($submission->complianceLinks as $link)
                                                        <a href="{{ $link->url }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-info"
                                                           title="{{ $link->title }}">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No links</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#submissionModal" 
                                                        onclick="viewSubmission({{ $submission->id }})"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Submissions Found</h5>
                            <p class="text-muted">{{ $facultyUser->name }} has not made any compliance submissions yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 600;
}

.stat-label {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endsection
