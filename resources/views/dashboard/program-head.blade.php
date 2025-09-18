<!-- Program Head Dashboard -->
<div class="row">
    <!-- Program Stats -->
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Faculty Submitted Documents</h5>
                        <h3 class="mb-0">{{ $dashboardData['faculty_submitted_documents'] ?? 0 }}</h3>
                        <small>Total compliance submissions</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-3x opacity-75"></i>
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
                        <h3 class="mb-0">{{ $dashboardData['faculty_assigned'] ?? 0 }}</h3>
                        <small>Faculty in your program</small>
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
                        <h5 class="card-title">Program Subjects</h5>
                        <h3 class="mb-0">{{ $dashboardData['program_subjects'] ?? 0 }}</h3>
                        <small>Subjects in your program</small>
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
    <!-- Compliance Status Overview -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Compliance Status Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-warning mb-1">{{ $dashboardData['pending_approvals'] ?? 0 }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success mb-1">{{ $dashboardData['approved_submissions'] ?? 0 }}</h4>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger mb-1">{{ $dashboardData['needs_revision'] ?? 0 }}</h4>
                        <small class="text-muted">Needs Revision</small>
                    </div>
                </div>
                <div class="mt-3">
                    <canvas id="complianceStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Compliance Trend -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Monthly Submission Trend
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compliance Status Pie Chart
    const complianceData = @json($dashboardData['compliance_analytics'] ?? ['pending' => 0, 'approved' => 0, 'needs_revision' => 0]);
    
    const ctx1 = document.getElementById('complianceStatusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Needs Revision'],
            datasets: [{
                data: [complianceData.pending, complianceData.approved, complianceData.needs_revision],
                backgroundColor: [
                    '#ffc107',  // warning - pending
                    '#28a745',  // success - approved
                    '#dc3545'   // danger - needs revision
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Monthly Trend Line Chart
    const monthlyData = @json($dashboardData['monthly_trend'] ?? []);
    
    const ctx2 = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Submissions',
                data: monthlyData.map(item => item.submissions),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
