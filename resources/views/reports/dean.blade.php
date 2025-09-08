@extends('layouts.app')

@section('title', 'Dean Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-file-pdf me-2"></i>Department Reports</h2>
                    <p class="text-muted">Compliance reports for {{ $department->name }}</p>
                </div>
                <div>
                    <a href="{{ route('reports.dean.pdf') }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-1"></i>Generate PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $department->name }} Overview</h4>
                            <p class="mb-0">{{ $department->description ?? 'Department compliance report overview' }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h3>{{ $departmentStats['compliance_rate'] }}% Compliance</h3>
                            <small>{{ $departmentStats['approved_submissions'] }}/{{ $departmentStats['total_submissions'] }} Approved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $departmentStats['total_programs'] }}</h4>
                    <small>Total Programs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $departmentStats['total_faculty'] }}</h4>
                    <small>Total Faculty</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $departmentStats['total_submissions'] }}</h4>
                    <small>Total Submissions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4>{{ $departmentStats['approved_submissions'] }}</h4>
                    <small>Approved Submissions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Program Compliance Report</h5>
        </div>
        <div class="card-body">
            @if($programStats->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Faculty Count</th>
                            <th>Total Submissions</th>
                            <th>Approved</th>
                            <th>Pending</th>
                            <th>Rejected</th>
                            <th>Compliance Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programStats as $programData)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $programData['program']->name }}</strong>
                                    @if($programData['program']->description)
                                    <br>
                                    <small class="text-muted">{{ $programData['program']->description }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $programData['total_faculty'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $programData['total_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $programData['approved_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $programData['pending_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $programData['rejected_submissions'] }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar 
                                            @if($programData['compliance_rate'] >= 80) bg-success 
                                            @elseif($programData['compliance_rate'] >= 60) bg-warning 
                                            @else bg-danger @endif" 
                                            style="width: {{ $programData['compliance_rate'] }}%"></div>
                                    </div>
                                    <small><strong>{{ $programData['compliance_rate'] }}%</strong></small>
                                </div>
                            </td>
                            <td>
                                @if($programData['compliance_rate'] >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($programData['compliance_rate'] >= 60)
                                    <span class="badge bg-warning">Good</span>
                                @elseif($programData['compliance_rate'] >= 40)
                                    <span class="badge bg-orange">Needs Improvement</span>
                                @else
                                    <span class="badge bg-danger">Critical</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5>No Programs Found</h5>
                <p class="text-muted">This department doesn't have any programs assigned yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-orange {
    background-color: #fd7e14 !important;
}
</style>
@endsection
