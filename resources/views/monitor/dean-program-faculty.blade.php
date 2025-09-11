@extends('layouts.app')

@section('title', 'Program Faculty Compliance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('monitor.faculty') }}">Monitor Faculty</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $program->name }}</li>
                </ol>
            </nav>
            <h2><i class="fas fa-clipboard-check me-2"></i>Monitor Faculty Compliance</h2>
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
                            <a href="{{ route('reports.dean') }}" class="btn btn-light btn-sm mt-2">
                                <i class="fas fa-file-pdf me-1"></i>Generate Report
                            </a>
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
                        <div class="mt-2">
                            @if($data['compliance_rate'] >= 80)
                                <span class="badge bg-success">Excellent ({{ $data['compliance_rate'] }}%)</span>
                            @elseif($data['compliance_rate'] >= 60)
                                <span class="badge bg-warning">Good ({{ $data['compliance_rate'] }}%)</span>
                            @elseif($data['compliance_rate'] >= 40)
                                <span class="badge bg-orange">Needs Improvement ({{ $data['compliance_rate'] }}%)</span>
                            @else
                                <span class="badge bg-danger">Critical ({{ $data['compliance_rate'] }}%)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Semester-wide Requirements Compliance Table -->
                <h6 class="mb-3">
                    <i class="fas fa-calendar me-2"></i>
                    Semester-wide Requirements Compliance
                    @if(isset($currentSemester) && $currentSemester)
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
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data['semester_compliances']) && $data['semester_compliances']->count() > 0)
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
                                        @if($compliance->approval_status)
                                            @if($compliance->approval_status === 'approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            @elseif($compliance->approval_status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Rejected
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pending Review
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not Reviewed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No semester requirements found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Assigned Subjects -->
                @if(isset($data['assigned_subjects']) && $data['assigned_subjects']->count() > 0)
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
                                                <th>Approval Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($subjectData['compliances']) && $subjectData['compliances']->count() > 0)
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
                                                        @if($compliance->approval_status)
                                                            @if($compliance->approval_status === 'approved')
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check-circle"></i> Approved
                                                                </span>
                                                            @elseif($compliance->approval_status === 'rejected')
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-times-circle"></i> Rejected
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning">
                                                                    <i class="fas fa-clock"></i> Pending Review
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">Not Reviewed</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No subject requirements found</td>
                                                </tr>
                                            @endif
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
                        This faculty member has no assigned subjects for the current semester.
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                <h5>No Faculty Found</h5>
                <p class="text-muted">This program doesn't have any faculty members assigned yet.</p>
            </div>
        </div>
    @endif
</div>

<style>
.bg-orange {
    background-color: #fd7e14 !important;
}

.toggle-subject-compliance {
    transition: all 0.3s ease;
}

.toggle-subject-compliance:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>
@endsection
