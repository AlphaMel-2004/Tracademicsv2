<!-- Faculty Dashboard -->
<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card                        </div>
                        <small class="text-muted">Just now</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showDeadlines() {
    alert('Deadlines feature coming soon!');
}
</script>
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">My Submissions</h5>
                        <h3 class="mb-0">{{ $dashboardData['my_submissions'] ?? 0 }}</h3>
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

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Required Docs</h5>
                        <h3 class="mb-0">{{ $dashboardData['document_types']->where('is_required', true)->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-list-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-upload me-2"></i>
                            Submit New Document
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach(\App\Models\DocumentType::all() as $docType)
                            <li>
                                <a class="dropdown-item" href="{{ route('compliance.create', ['document_type_id' => $docType->id]) }}">
                                    {{ $docType->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('compliance.my-submissions') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        View My Submissions
                    </a>
                    <a href="#" class="btn btn-outline-secondary" onclick="showDeadlines()">
                        <i class="fas fa-calendar me-2"></i>
                        View Deadlines
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Widget -->
    <div class="col-md-4 mb-4">
        @include('components.notification-widget')
    </div>

    <!-- Document Requirements -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Document Requirements
                </h5>
            </div>
            <div class="card-body">
                @if($dashboardData['document_types'] ?? false)
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Semester-wide Requirements</h6>
                        <ul class="list-unstyled">
                            @foreach($dashboardData['document_types']->where('submission_type', 'semester') as $docType)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-file me-2 text-muted"></i>
                                        {{ $docType->name }}
                                    </span>
                                    <span class="status-badge status-pending">Pending</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Subject-specific Requirements</h6>
                        <ul class="list-unstyled">
                            @foreach($dashboardData['document_types']->where('submission_type', 'subject')->take(5) as $docType)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-file me-2 text-muted"></i>
                                        {{ $docType->name }}
                                    </span>
                                    <span class="status-badge status-pending">Pending</span>
                                </div>
                            </li>
                            @endforeach
                            @if($dashboardData['document_types']->where('submission_type', 'subject')->count() > 5)
                            <li class="text-muted small">
                                <i class="fas fa-ellipsis-h me-2"></i>
                                And {{ $dashboardData['document_types']->where('submission_type', 'subject')->count() - 5 }} more...
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                @else
                <p class="text-muted mb-0">No document requirements found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Welcome to TracAdemics!</h6>
                            <p class="mb-1 text-muted">Start by submitting your required documents for the current semester.</p>
                            <small class="text-muted">{{ now()->format('M j, Y \a\t g:i A') }}</small>
                        </div>
                        <span class="badge bg-info">Info</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
