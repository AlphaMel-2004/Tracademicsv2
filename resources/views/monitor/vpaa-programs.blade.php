@extends('layouts.app')

@section('title', 'Monitor Programs')

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
                    <li class="breadcrumb-item active">{{ $department->name }}</li>
                </ol>
            </nav>
            <h2><i class="fas fa-building me-2"></i>{{ $department->name }} Programs</h2>
            <p class="text-muted">{{ $department->description ?? 'Monitor compliance status for all programs' }}</p>
        </div>
    </div>

    <!-- Programs Grid -->
    <div class="row">
        @forelse($programs as $programData)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $programData['program']->name }}</h5>
                    <span class="badge badge-compliance compliance-{{ $programData['compliance_rate'] >= 80 ? 'high' : ($programData['compliance_rate'] >= 60 ? 'medium' : 'low') }}">
                        {{ $programData['compliance_rate'] }}%
                    </span>
                </div>
                <div class="card-body">
                    @if($programData['program']->description)
                    <p class="text-muted small mb-3">{{ $programData['program']->description }}</p>
                    @endif
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h6 class="text-muted mb-1">Faculty</h6>
                            <h4 class="text-success">{{ $programData['total_faculty'] }}</h4>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted mb-1">Submissions</h6>
                            <h4 class="text-info">{{ $programData['total_submissions'] }}</h4>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $programData['compliance_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">Compliance Rate: {{ $programData['compliance_rate'] }}%</small>
                    </div>
                    
                    <!-- Submission Stats -->
                    <div class="text-center">
                        <span class="badge bg-success me-1">{{ $programData['approved_submissions'] }} Approved</span>
                        <span class="badge bg-warning">{{ $programData['total_submissions'] - $programData['approved_submissions'] }} Pending/Others</span>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('monitor.program.faculty', $programData['program']) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-users me-1"></i>View Faculty
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <h5>No Programs Found</h5>
                    <p class="text-muted">This department doesn't have any programs assigned yet.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.badge-compliance {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}

.compliance-high {
    background-color: #28a745;
    color: white;
}

.compliance-medium {
    background-color: #ffc107;
    color: #212529;
}

.compliance-low {
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
@endsection
