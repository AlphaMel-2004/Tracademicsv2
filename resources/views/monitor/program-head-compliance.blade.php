@extends('layouts.app')

@section('title', 'Monitor Compliances')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-clipboard-check me-2"></i>Monitor Compliances</h2>
            <p class="text-muted">Faculty compliance status for {{ $program->name }}</p>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="facultySearch" placeholder="Search faculty by name...">
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="excellent">Excellent (80%+)</option>
                <option value="good">Good (60-79%)</option>
                <option value="needs-improvement">Needs Improvement (40-59%)</option>
                <option value="critical">Critical (Below 40%)</option>
            </select>
        </div>
    </div>

    <!-- Faculty Compliance Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Faculty Compliance Details</h5>
        </div>
        <div class="card-body">
            @if($facultyCompliance->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="facultyTable">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facultyCompliance as $facultyData)
                        <tr data-status="{{ $facultyData['compliance_rate'] >= 80 ? 'excellent' : ($facultyData['compliance_rate'] >= 60 ? 'good' : ($facultyData['compliance_rate'] >= 40 ? 'needs-improvement' : 'critical')) }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        {{ substr($facultyData['faculty']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong class="faculty-name">{{ $facultyData['faculty']->name }}</strong>
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
                                        <div class="progress-bar 
                                            @if($facultyData['compliance_rate'] >= 80) bg-success 
                                            @elseif($facultyData['compliance_rate'] >= 60) bg-warning 
                                            @elseif($facultyData['compliance_rate'] >= 40) bg-orange 
                                            @else bg-danger @endif" 
                                            style="width: {{ $facultyData['compliance_rate'] }}%"></div>
                                    </div>
                                    <small><strong>{{ $facultyData['compliance_rate'] }}%</strong></small>
                                </div>
                            </td>
                            <td>
                                @if($facultyData['last_submission'])
                                    <small>{{ $facultyData['last_submission']->created_at->format('M j, Y') }}</small>
                                    <br>
                                    <span class="badge badge-sm bg-{{ $facultyData['last_submission']->status === 'approved' ? 'success' : ($facultyData['last_submission']->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($facultyData['last_submission']->status) }}
                                    </span>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('compliance.user-submissions', $facultyData['faculty']->id) }}" class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($facultyData['compliance_rate'] < 60)
                                    <button class="btn btn-outline-warning" title="Send Reminder" onclick="sendReminder({{ $facultyData['faculty']->id }})">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                    @endif
                                </div>
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

.badge-sm {
    font-size: 0.7em;
}

.avatar-initial {
    font-weight: bold;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('facultySearch');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('facultyTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        rows.forEach(row => {
            const facultyName = row.querySelector('.faculty-name').textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            
            const matchesSearch = facultyName.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
});

function sendReminder(facultyId) {
    // Implement reminder functionality
    alert('Reminder sent to faculty member!');
}
</script>
@endsection
