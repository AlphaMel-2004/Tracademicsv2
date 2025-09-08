<!-- MIS Dashboard -->
<div class="row">
    <!-- System Overview Stats -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h3 class="mb-0">{{ $dashboardData['total_users'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Submissions</h5>
                        <h3 class="mb-0">{{ $dashboardData['total_submissions'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Review</h5>
                        <h3 class="mb-0">{{ $dashboardData['pending_submissions'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Approved</h5>
                        <h3 class="mb-0">{{ $dashboardData['approved_submissions'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Admin Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    System Administration
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>
                        Manage Users
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-building me-2"></i>
                        Manage Departments
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <img src="{{ asset('images/tracademics-logo.png') }}" alt="Programs" style="width: 16px; height: 16px;" class="me-2">
                        Manage Programs
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar me-2"></i>
                        Manage Semesters
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-file-contract me-2"></i>
                        Document Types
                    </a>
                    <a href="{{ route('compliance.review') }}" class="btn btn-success">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Review Submissions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Reports -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    System Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>
                        Compliance Summary
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-users me-2"></i>
                        User Activity Report
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-building me-2"></i>
                        Department Overview
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-chart-line me-2"></i>
                        Performance Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Semester Info -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar me-2"></i>
                    Current Semester
                </h5>
            </div>
            <div class="card-body">
                @if($dashboardData['active_semester'] ?? null)
                <h6 class="text-primary">{{ $dashboardData['active_semester']->name }}</h6>
                <p class="text-muted mb-2">{{ $dashboardData['active_semester']->academic_year }}</p>
                <hr>
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Start Date:</span>
                        <span>{{ $dashboardData['active_semester']->start_date->format('M j, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>End Date:</span>
                        <span>{{ $dashboardData['active_semester']->end_date->format('M j, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status:</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                @else
                <p class="text-muted">No active semester found</p>
                <a href="#" class="btn btn-primary btn-sm">Set Active Semester</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent System Activity -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent System Activity
                </h5>
                <a href="{{ route('settings.user-logs') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recent-activity-table">
                            @if(isset($dashboardData['recent_activities']) && $dashboardData['recent_activities']->count() > 0)
                                @foreach($dashboardData['recent_activities'] as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $activity->user->name }}</div>
                                                <small class="text-muted">{{ $activity->user->role->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge bg-{{ $activity->action == 'login' ? 'success' : ($activity->action == 'logout' ? 'secondary' : ($activity->action == 'create' ? 'primary' : ($activity->action == 'update' ? 'info' : 'warning'))) }}">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                            <div class="small text-muted mt-1">{{ $activity->description }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $activity->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $activity->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                        No recent activity to display
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh recent activity every 30 seconds
setInterval(function() {
    fetch('{{ route("dashboard") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.text())
    .then(data => {
        // Parse the response to extract just the recent activity table
        const parser = new DOMParser();
        const doc = parser.parseFromString(data, 'text/html');
        const newActivityTable = doc.querySelector('#recent-activity-table');
        
        if (newActivityTable) {
            const currentActivityTable = document.querySelector('#recent-activity-table');
            if (currentActivityTable) {
                currentActivityTable.innerHTML = newActivityTable.innerHTML;
            }
        }
    })
    .catch(error => {
        console.log('Auto-refresh failed:', error);
    });
}, 30000); // Refresh every 30 seconds

// Add visual indicator for real-time updates
document.addEventListener('DOMContentLoaded', function() {
    const activityHeader = document.querySelector('.card-header h5');
    if (activityHeader) {
        const indicator = document.createElement('small');
        indicator.className = 'text-muted ms-2';
        indicator.innerHTML = '<i class="fas fa-circle text-success" style="font-size: 6px;"></i> Live';
        activityHeader.appendChild(indicator);
    }
});
</script>
