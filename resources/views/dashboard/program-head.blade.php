<!-- Program Head Dashboard -->
<div class="row">
    <!-- Program Stats -->
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Program Submissions</h5>
                        <h3 class="mb-0">{{ $dashboardData['program_submissions'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Faculty Assigned</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Subjects</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Program Management -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Program Management
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Assign Faculty to Subjects
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>
                        Review Faculty Submissions
                    </a>
                    <a href="{{ route('compliance.review') }}" class="btn btn-primary">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Review Submissions
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-book me-2"></i>
                        Manage Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Reports -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Program Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Program Overview
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-users me-2"></i>
                        Faculty Performance
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-check-circle me-2"></i>
                        Compliance Status
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
