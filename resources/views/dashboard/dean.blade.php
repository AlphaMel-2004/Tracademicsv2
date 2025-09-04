<!-- Dean Dashboard -->
<div class="row">
    <!-- Department Stats -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Dept. Submissions</h5>
                        <h3 class="mb-0">{{ $dashboardData['department_submissions'] ?? 0 }}</h3>
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
                        <h5 class="card-title">Pending</h5>
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
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Faculty Count</h5>
                        <h3 class="mb-0">{{ $dashboardData['faculty_count'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
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
                        <h5 class="card-title">My Department</h5>
                        <h3 class="mb-0">{{ $user->department->code ?? 'N/A' }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Department Management -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users-cog me-2"></i>
                    Department Management
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('compliance.review') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>
                        Review Department Submissions
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>
                        Manage Faculty
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-user-tie me-2"></i>
                        Program Heads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Reports -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Department Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>
                        Department Overview
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Compliance Report
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-users me-2"></i>
                        Faculty Performance
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
