<!-- Enhanced Faculty Dashboard -->
<div class="faculty-dashboard">
<div class="row">
    <!-- Quick Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Submissions</h6>
                        <h3 class="mb-0">{{ $dashboardData['my_submissions'] ?? 0 }}</h3>
                        <small class="opacity-75">This semester</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Pending Review</h6>
                        <h3 class="mb-0">{{ $dashboardData['pending_submissions'] ?? 0 }}</h3>
                        <small class="opacity-75">Awaiting approval</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Approved</h6>
                        <h3 class="mb-0">{{ $dashboardData['approved_submissions'] ?? 0 }}</h3>
                        <small class="opacity-75">Successfully reviewed</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Compliance Rate</h6>
                        <h3 class="mb-0">{{ $dashboardData['compliance_rate'] ?? 0 }}%</h3>
                        <small class="opacity-75">Overall progress</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compliance Progress -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Compliance Progress
                </h5>
            </div>
            <div class="card-body">
                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $dashboardData['compliance_rate'] ?? 0 }}%" 
                         aria-valuenow="{{ $dashboardData['compliance_rate'] ?? 0 }}" 
                         aria-valuemin="0" aria-valuemax="100">
                        {{ $dashboardData['compliance_rate'] ?? 0 }}% Complete
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-3">
                        <small class="text-muted">Total Required</small>
                        <br><strong>{{ $dashboardData['document_types']->where('is_required', true)->count() ?? 0 }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Approved</small>
                        <br><strong class="text-success">{{ $dashboardData['approved_submissions'] ?? 0 }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Pending</small>
                        <br><strong class="text-warning">{{ $dashboardData['pending_submissions'] ?? 0 }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Under Review</small>
                        <br><strong class="text-info">{{ $dashboardData['under_review_submissions'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
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
                            @forelse($dashboardData['document_types'] ?? [] as $docType)
                            <li>
                                <a class="dropdown-item" href="{{ route('compliance.create', ['document_type_id' => $docType->id]) }}">
                                    <i class="fas fa-file me-2"></i>{{ $docType->name }}
                                </a>
                            </li>
                            @empty
                            <li><span class="dropdown-item text-muted">No document types available</span></li>
                            @endforelse
                        </ul>
                    </div>
                    <a href="{{ route('compliance.my-submissions') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        View My Submissions
                    </a>
                    <button class="btn btn-outline-secondary" onclick="showDeadlines()">
                        <i class="fas fa-calendar me-2"></i>
                        View Deadlines
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-info">
                        <i class="fas fa-user me-2"></i>
                        Update Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Submissions
                </h5>
            </div>
            <div class="card-body">
                @if(isset($dashboardData['my_recent_submissions']) && $dashboardData['my_recent_submissions']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($dashboardData['my_recent_submissions'] as $submission)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $submission->documentType->name ?? 'Unknown Document' }}</h6>
                                    <p class="mb-1 text-muted small">{{ $submission->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="badge bg-{{ $submission->status == 'approved' ? 'success' : ($submission->status == 'pending' ? 'warning' : ($submission->status == 'rejected' ? 'danger' : 'info')) }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-alt fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No submissions yet</p>
                        <small>Start by submitting your first document</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Document Requirements -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Document Requirements
                </h5>
            </div>
            <div class="card-body">
                @if(isset($dashboardData['semester_documents']) && $dashboardData['semester_documents']->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-calendar me-1"></i>
                            Semester Requirements ({{ $dashboardData['semester_documents']->count() }})
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($dashboardData['semester_documents']->take(3) as $docType)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">
                                        <i class="fas fa-file me-2 text-muted"></i>
                                        {{ $docType->name }}
                                    </span>
                                    @if($docType->is_required)
                                        <span class="badge bg-danger badge-sm">Required</span>
                                    @else
                                        <span class="badge bg-secondary badge-sm">Optional</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @if($dashboardData['semester_documents']->count() > 3)
                            <div class="list-group-item px-0 py-2">
                                <small class="text-muted">
                                    <i class="fas fa-ellipsis-h me-2"></i>
                                    And {{ $dashboardData['semester_documents']->count() - 3 }} more...
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if(isset($dashboardData['subject_documents']) && $dashboardData['subject_documents']->count() > 0)
                    <div>
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-book me-1"></i>
                            Subject Requirements ({{ $dashboardData['subject_documents']->count() }})
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($dashboardData['subject_documents']->take(3) as $docType)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">
                                        <i class="fas fa-file me-2 text-muted"></i>
                                        {{ $docType->name }}
                                    </span>
                                    @if($docType->is_required)
                                        <span class="badge bg-danger badge-sm">Required</span>
                                    @else
                                        <span class="badge bg-secondary badge-sm">Optional</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @if($dashboardData['subject_documents']->count() > 3)
                            <div class="list-group-item px-0 py-2">
                                <small class="text-muted">
                                    <i class="fas fa-ellipsis-h me-2"></i>
                                    And {{ $dashboardData['subject_documents']->count() - 3 }} more...
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if((!isset($dashboardData['semester_documents']) || $dashboardData['semester_documents']->count() == 0) && 
                    (!isset($dashboardData['subject_documents']) || $dashboardData['subject_documents']->count() == 0))
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No requirements found</p>
                        <small>Contact your administrator</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Current Semester & Subject Information -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Current Semester Information
                </h5>
            </div>
            <div class="card-body">
                @if($dashboardData['active_semester'])
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">{{ $dashboardData['active_semester']->name }}</h6>
                            <p class="text-muted mb-2">{{ $dashboardData['active_semester']->academic_year }}</p>
                        </div>
                        <div class="col-md-6">
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
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No active semester</p>
                        <small>Contact your administrator</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    My Assigned Subjects
                </h5>
            </div>
            <div class="card-body">
                @if(isset($dashboardData['my_subjects']) && $dashboardData['my_subjects']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($dashboardData['my_subjects'] as $assignment)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $assignment->subject->name ?? 'Unknown Subject' }}</h6>
                                    <small class="text-muted">Code: {{ $assignment->subject->code ?? 'N/A' }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $assignment->subject->units ?? 0 }} units</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-book-open fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No subjects assigned</p>
                        <small>Contact your program head for subject assignments</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showDeadlines() {
    // Create a modal to show upcoming deadlines
    const modal = `
        <div class="modal fade" id="deadlinesModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar me-2"></i>
                            Upcoming Deadlines
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if(isset($dashboardData['upcoming_deadlines']) && $dashboardData['upcoming_deadlines']->count() > 0)
                            <div class="list-group">
                                @foreach($dashboardData['upcoming_deadlines'] as $deadline)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $deadline->name }}</h6>
                                            <small class="text-muted">Due in {{ $deadline->due_days }} days from semester start</small>
                                        </div>
                                        <span class="badge bg-warning">Upcoming</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                <p class="mb-0">No upcoming deadlines</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('compliance.my-submissions') }}" class="btn btn-primary">View All Submissions</a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('deadlinesModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Show modal
    const bsModal = new bootstrap.Modal(document.getElementById('deadlinesModal'));
    bsModal.show();
}
</script>
</div> <!-- End faculty-dashboard -->
