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
    <!-- Program Analytics Chart -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Program Analytics - {{ $user->department->name ?? 'Department' }}
                        </h5>
                        <small class="text-light opacity-75">Compliance submissions overview by program</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="chartOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chartOptionsDropdown">
                            <li><a class="dropdown-item" href="#" onclick="toggleChartType()"><i class="fas fa-chart-line me-2"></i>Switch to Line Chart</a></li>
                            <li><a class="dropdown-item" href="#" onclick="toggleChartAnimation()"><i class="fas fa-play me-2"></i>Toggle Animation</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportChart()"><i class="fas fa-download me-2"></i>Export Chart</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-4" style="min-height: 400px;">
                <!-- Chart Container -->
                <div id="chartContainer">
                    <canvas id="programAnalyticsChart" width="400" height="300"></canvas>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fas fa-chart-bar text-muted" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                    <h5 class="text-muted">No Data Available</h5>
                    <p class="text-muted">No compliance submissions found for programs in this department.</p>
                </div>
                
                <!-- Chart Summary -->
                <div id="chartSummary" class="mt-4 p-3 bg-light rounded" style="display: none;">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 id="totalPrograms" class="text-primary mb-0">0</h4>
                                <small class="text-muted">Total Programs</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 id="totalSubmissions" class="text-info mb-0">0</h4>
                                <small class="text-muted">Total Submissions</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 id="approvalRate" class="text-success mb-0">0%</h4>
                                <small class="text-muted">Approval Rate</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 id="pendingCount" class="text-warning mb-0">0</h4>
                                <small class="text-muted">Pending Reviews</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartInstance = null;
let currentChartType = 'bar';
let animationEnabled = true;

document.addEventListener('DOMContentLoaded', function() {
    initializeChart();
});

function initializeChart() {
    const programData = @json($dashboardData['program_analytics'] ?? []);
    
    if (programData.length === 0 || programData.every(item => item.total_submissions === 0)) {
        document.getElementById('emptyState').style.display = 'block';
        return;
    }
    
    document.getElementById('chartSummary').style.display = 'block';
    
    // Update summary statistics
    updateSummaryStats(programData);
    
    // Create the chart
    createChart(programData);
}

function createChart(programData) {
    const ctx = document.getElementById('programAnalyticsChart').getContext('2d');
    
    // Prepare data
    const labels = programData.map(item => {
        const name = item.program_name || 'Unknown Program';
        return name.length > 20 ? name.substring(0, 20) + '...' : name;
    });
    
    const fullLabels = programData.map(item => item.program_name || 'Unknown Program');
    const submissionsData = programData.map(item => item.total_submissions || 0);
    const approvedData = programData.map(item => item.approved_submissions || 0);
    const pendingData = programData.map(item => item.pending_submissions || 0);
    const needsRevisionData = programData.map(item => item.needs_revision_submissions || 0);
    
    // Enhanced color scheme
    const colors = {
        total: {
            background: 'rgba(99, 102, 241, 0.8)',
            border: 'rgba(99, 102, 241, 1)',
            hover: 'rgba(99, 102, 241, 0.9)'
        },
        approved: {
            background: 'rgba(34, 197, 94, 0.8)',
            border: 'rgba(34, 197, 94, 1)',
            hover: 'rgba(34, 197, 94, 0.9)'
        },
        pending: {
            background: 'rgba(251, 191, 36, 0.8)',
            border: 'rgba(251, 191, 36, 1)',
            hover: 'rgba(251, 191, 36, 0.9)'
        },
        revision: {
            background: 'rgba(239, 68, 68, 0.8)',
            border: 'rgba(239, 68, 68, 1)',
            hover: 'rgba(239, 68, 68, 0.9)'
        }
    };
    
    chartInstance = new Chart(ctx, {
        type: currentChartType,
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Submissions',
                data: submissionsData,
                backgroundColor: colors.total.background,
                borderColor: colors.total.border,
                hoverBackgroundColor: colors.total.hover,
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }, {
                label: 'Approved',
                data: approvedData,
                backgroundColor: colors.approved.background,
                borderColor: colors.approved.border,
                hoverBackgroundColor: colors.approved.hover,
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }, {
                label: 'Pending Review',
                data: pendingData,
                backgroundColor: colors.pending.background,
                borderColor: colors.pending.border,
                hoverBackgroundColor: colors.pending.hover,
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }, {
                label: 'Needs Revision',
                data: needsRevisionData,
                backgroundColor: colors.revision.background,
                borderColor: colors.revision.border,
                hoverBackgroundColor: colors.revision.hover,
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            animation: {
                duration: animationEnabled ? 1500 : 0,
                easing: 'easeOutQuart',
                onComplete: function() {
                    // Add a subtle pulse effect to bars with data
                    if (animationEnabled) {
                        this.update('none');
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Compliance Submissions by Academic Program',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return fullLabels[context[0].dataIndex];
                        },
                        label: function(context) {
                            const value = context.parsed.y;
                            const total = submissionsData[context.dataIndex];
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0';
                            return `${context.dataset.label}: ${value} (${percentage}%)`;
                        },
                        afterBody: function(context) {
                            const index = context[0].dataIndex;
                            const program = programData[index];
                            const rate = program.total_submissions > 0 ? 
                                ((program.approved_submissions / program.total_submissions) * 100).toFixed(1) : '0';
                            return [``, `Program Code: ${program.program_code}`, `Approval Rate: ${rate}%`];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Number of Submissions',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            onHover: (event, activeElements) => {
                event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
            },
            onClick: (event, activeElements) => {
                if (activeElements.length > 0) {
                    const dataIndex = activeElements[0].index;
                    const program = programData[dataIndex];
                    showProgramDetails(program);
                }
            }
        }
    });
}

function updateSummaryStats(programData) {
    const totalPrograms = programData.length;
    const totalSubmissions = programData.reduce((sum, p) => sum + (p.total_submissions || 0), 0);
    const totalApproved = programData.reduce((sum, p) => sum + (p.approved_submissions || 0), 0);
    const totalPending = programData.reduce((sum, p) => sum + (p.pending_submissions || 0), 0);
    const approvalRate = totalSubmissions > 0 ? ((totalApproved / totalSubmissions) * 100).toFixed(1) : 0;
    
    document.getElementById('totalPrograms').textContent = totalPrograms;
    document.getElementById('totalSubmissions').textContent = totalSubmissions;
    document.getElementById('approvalRate').textContent = approvalRate + '%';
    document.getElementById('pendingCount').textContent = totalPending;
}

function showProgramDetails(program) {
    const modal = `
        <div class="modal fade" id="programModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-graduation-cap me-2"></i>
                            ${program.program_name}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded">
                                    <h4 class="text-primary">${program.total_submissions}</h4>
                                    <small>Total Submissions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                                    <h4 class="text-success">${program.approved_submissions}</h4>
                                    <small>Approved</small>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="stat-card text-center p-3 bg-warning bg-opacity-10 rounded">
                                    <h4 class="text-warning">${program.pending_submissions}</h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="stat-card text-center p-3 bg-danger bg-opacity-10 rounded">
                                    <h4 class="text-danger">${program.needs_revision_submissions}</h4>
                                    <small>Needs Revision</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p><strong>Program Code:</strong> ${program.program_code}</p>
                        <p><strong>Approval Rate:</strong> ${program.total_submissions > 0 ? ((program.approved_submissions / program.total_submissions) * 100).toFixed(1) : 0}%</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('programModal');
    if (existingModal) existingModal.remove();
    
    // Add new modal
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('programModal')).show();
}

function toggleChartType() {
    currentChartType = currentChartType === 'bar' ? 'line' : 'bar';
    chartInstance.config.type = currentChartType;
    chartInstance.update('active');
    
    const dropdownText = currentChartType === 'bar' ? 'Switch to Line Chart' : 'Switch to Bar Chart';
    event.target.innerHTML = `<i class="fas fa-chart-${currentChartType === 'bar' ? 'line' : 'bar'} me-2"></i>${dropdownText}`;
}

function toggleChartAnimation() {
    animationEnabled = !animationEnabled;
    chartInstance.options.animation.duration = animationEnabled ? 1500 : 0;
    
    const dropdownText = animationEnabled ? 'Disable Animation' : 'Enable Animation';
    const icon = animationEnabled ? 'pause' : 'play';
    event.target.innerHTML = `<i class="fas fa-${icon} me-2"></i>${dropdownText}`;
}

function exportChart() {
    const url = chartInstance.toBase64Image('image/png', 1.0, '#ffffff');
    const link = document.createElement('a');
    link.download = 'program-analytics-chart.png';
    link.href = url;
    link.click();
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.stat-item {
    padding: 0.5rem;
}

.stat-item h4 {
    font-weight: 600;
    font-size: 1.5rem;
}

.modal .stat-card h4 {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

#programAnalyticsChart {
    max-height: 300px;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1) !important;
}
</style>
