@extends('layouts.app')

@section('title', 'Faculty Compliance Status')

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
            <h2><i class="fas fa-users me-2"></i>{{ $program->name }} Faculty</h2>
            <p class="text-muted">Compliance status for all faculty members in this program</p>
        </div>
    </div>

    <!-- Faculty Compliance Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Faculty Compliance Overview</h5>
        </div>
        <div class="card-body">
            @if($facultyCompliance->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Faculty Member</th>
                            <th>Total Submissions</th>
                            <th>Approved</th>
                            <th>Pending</th>
                            <th>Rejected</th>
                            <th>Compliance Rate</th>
                            <th>Last Submission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facultyCompliance as $facultyData)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        {{ substr($facultyData['faculty']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $facultyData['faculty']->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $facultyData['faculty']->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $facultyData['total_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $facultyData['approved_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $facultyData['pending_submissions'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $facultyData['rejected_submissions'] }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $facultyData['compliance_rate'] }}%"></div>
                                    </div>
                                    <small>{{ $facultyData['compliance_rate'] }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($facultyData['last_submission'])
                                    <small>{{ $facultyData['last_submission']->created_at->format('M j, Y') }}</small>
                                @else
                                    <small class="text-muted">No submissions</small>
                                @endif
                            </td>
                            <td>
                                @if($facultyData['compliance_rate'] >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($facultyData['compliance_rate'] >= 60)
                                    <span class="badge bg-warning">Good</span>
                                @elseif($facultyData['compliance_rate'] >= 40)
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
                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                <h5>No Faculty Found</h5>
                <p class="text-muted">This program doesn't have any faculty members assigned yet.</p>
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
