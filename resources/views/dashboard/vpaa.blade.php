<!-- VPAA Dashboard -->
<div class="row">
    <!-- University-wide Stats -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
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
                        <h3 class="mb-0">{{ $dashboardData['pending_reviews'] ?? 0 }}</h3>
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
                        <h5 class="card-title">Compliance Rate</h5>
                        <h3 class="mb-0">{{ $dashboardData['compliance_rate'] ?? 0 }}%</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
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
                        <h5 class="card-title">All Departments</h5>
                        <h3 class="mb-0">3</h3>
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
    <!-- Review Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Review & Approval
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('compliance.review') }}" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>
                        Review All Submissions
                    </a>
                    <a href="{{ route('compliance.review', ['status' => 'submitted']) }}" class="btn btn-outline-warning">
                        <i class="fas fa-clock me-2"></i>
                        Pending Approvals
                    </a>
                    <a href="{{ route('compliance.review', ['status' => 'approved']) }}" class="btn btn-outline-success">
                        <i class="fas fa-check me-2"></i>
                        Approved Submissions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- University Reports -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    University Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('monitor.index') }}" class="btn btn-success">
                        <i class="fas fa-monitor me-2"></i>
                        Monitor All Departments
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-building me-2"></i>
                        Department Compliance
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-users me-2"></i>
                        Faculty Performance
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monitor Quick Access -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    University-wide Compliance Monitoring
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Monitor compliance status across all departments and programs in the university.</p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-building fa-3x text-primary mb-2"></i>
                            <h6>All Departments</h6>
                            <p class="small text-muted">View department-wise compliance overview</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-graduation-cap fa-3x text-success mb-2"></i>
                            <h6>Programs & Faculty</h6>
                            <p class="small text-muted">Drill down to program and faculty levels</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-chart-line fa-3x text-info mb-2"></i>
                            <h6>Real-time Analytics</h6>
                            <p class="small text-muted">Live compliance rates and statistics</p>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('monitor.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-monitor me-2"></i>Start Monitoring
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
