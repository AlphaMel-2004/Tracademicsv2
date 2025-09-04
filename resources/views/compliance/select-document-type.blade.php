@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-upload me-2"></i>
                        Submit Documents
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Select the type of document you would like to submit for the current semester.
                    </p>

                    <div class="row">
                        @foreach($documentTypes as $docType)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm hover-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            @if($docType->submission_type === 'semester')
                                                <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                                            @elseif($docType->submission_type === 'subject')
                                                <i class="fas fa-book fa-3x text-success"></i>
                                            @else
                                                <i class="fas fa-file-alt fa-3x text-info"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title">{{ $docType->name }}</h6>
                                        @if($docType->description)
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($docType->description, 100) }}
                                            </p>
                                        @endif
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($docType->submission_type) }}
                                            </span>
                                            @if($docType->is_required)
                                                <span class="badge bg-danger">Required</span>
                                            @else
                                                <span class="badge bg-info">Optional</span>
                                            @endif
                                        </div>
                                        @php
                                            $currentSemester = \App\Models\Semester::where('is_active', true)->first();
                                            $deadline = $currentSemester ? \Carbon\Carbon::parse($currentSemester->start_date)->addDays($docType->due_days) : null;
                                        @endphp
                                        @if($deadline)
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Due: {{ $deadline->format('M d, Y') }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        @php
                                            // Check if user has already submitted this document
                                            $hasSubmitted = \App\Models\ComplianceSubmission::where('user_id', Auth::id())
                                                ->where('document_type_id', $docType->id)
                                                ->whereHas('semester', function($q) {
                                                    $q->where('is_active', true);
                                                })
                                                ->exists();
                                        @endphp
                                        
                                        @if($hasSubmitted)
                                            <div class="mb-2">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Submitted
                                                </span>
                                            </div>
                                            <a href="{{ route('compliance.my-submissions') }}" class="btn btn-outline-primary btn-sm">
                                                View Submission
                                            </a>
                                        @else
                                            <a href="{{ route('compliance.create', ['document_type_id' => $docType->id]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-upload me-1"></i>Submit Now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($documentTypes->count() === 0)
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Document Types Available</h5>
                            <p class="text-muted">Please contact your administrator.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('compliance.my-submissions') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i>View My Submissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.card-body {
    position: relative;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
