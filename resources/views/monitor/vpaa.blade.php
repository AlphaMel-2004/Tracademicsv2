@extends('layouts.app')

@section('title', 'Monitor All Departments')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-monitor me-2"></i>Monitor All Departments</h2>
            <p class="text-muted">Overview of compliance status across all departments</p>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h3>{{ $overallStats['total_users'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Submissions</h6>
                            <h3>{{ $overallStats['total_submissions'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Approved</h6>
                            <h3>{{ $overallStats['approved_submissions'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Overall Compliance</h6>
                            <h3>{{ $overallStats['compliance_rate'] }}%</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="row">
        @foreach($departments as $deptData)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $deptData['department']->name }}</h5>
                    <span class="badge badge-compliance compliance-{{ $deptData['compliance_rate'] >= 80 ? 'high' : ($deptData['compliance_rate'] >= 60 ? 'medium' : 'low') }}">
                        {{ $deptData['compliance_rate'] }}%
                    </span>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h6 class="text-muted mb-1">Programs</h6>
                            <h4 class="text-primary">{{ $deptData['total_programs'] }}</h4>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted mb-1">Faculty</h6>
                            <h4 class="text-success">{{ $deptData['total_faculty'] }}</h4>
                        </div>
                        <div class="col-4">
                            <h6 class="text-muted mb-1">Submissions</h6>
                            <h4 class="text-info">{{ $deptData['total_submissions'] }}</h4>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $deptData['compliance_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">Compliance Rate: {{ $deptData['compliance_rate'] }}%</small>
                    </div>
                    
                    <!-- Submission Stats -->
                    <div class="row text-center small">
                        <div class="col-4">
                            <span class="text-success">{{ $deptData['approved_submissions'] }} Approved</span>
                        </div>
                        <div class="col-4">
                            <span class="text-warning">{{ $deptData['pending_submissions'] }} Pending</span>
                        </div>
                        <div class="col-4">
                            <span class="text-muted">{{ $deptData['total_submissions'] - $deptData['approved_submissions'] - $deptData['pending_submissions'] }} Others</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('monitor.department', $deptData['department']) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>View Programs
                    </a>
                </div>
            </div>
        </div>
        @endforeach
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
