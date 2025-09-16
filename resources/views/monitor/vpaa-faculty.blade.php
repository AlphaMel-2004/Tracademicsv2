@extends('layouts.app')

@section('title', 'Faculty Compliance Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('monitor.index') }}">Monitor</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('monitor.department', $program->department) }}">{{ $program->department->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $program->name }}</li>
                </ol>
            </nav>
            <h2><i class="fas fa-eye me-2"></i>Faculty Compliance Details - {{ $program->name }}</h2>
            <p class="text-muted">Detailed view of compliance status for all faculty members (VPAA View-Only)</p>
        </div>
    </div>

    <!-- Program Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $program->name }}</h4>
                            <p class="mb-0">{{ $program->description ?? 'Program Overview' }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $facultyCompliance->count() }} Faculty Members</h3>
                            <small>VPAA View-Only Access</small>
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
                        <span class="badge bg-info">
                            <i class="fas fa-eye"></i> View Only
                        </span>
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
                                <th>View</th>
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
                                    <span class="badge bg-info">
                                        <i class="fas fa-eye"></i> VPAA View
                                    </span>
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
                                    <button class="btn btn-sm btn-outline-info w-100 toggle-subject-compliance" 
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
                                                <th>View</th>
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
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-eye"></i> VPAA View
                                                    </span>
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
@endsection
