@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-bar me-2"></i>Reports Dashboard</h2>
                <div class="btn-group" role="group">
                    <a href="{{ route('reports.compliance') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-alt me-1"></i>Compliance Report
                    </a>
                    <a href="{{ route('reports.faculty') }}" class="btn btn-outline-success">
                        <i class="fas fa-users me-1"></i>Faculty Report
                    </a>
                    <a href="{{ route('reports.department') }}" class="btn btn-outline-info">
                        <i class="fas fa-building me-1"></i>Department Report
                    </a>
                </div>
            </div>

            <!-- Overview Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Submissions</h6>
                                    <h3 class="mb-0">{{ $stats['total_submissions'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-file-upload fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Approved</h6>
                                    <h3 class="mb-0">{{ $stats['approved_submissions'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Pending</h6>
                                    <h3 class="mb-0">{{ $stats['pending_submissions'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Needs Revision</h6>
                                    <h3 class="mb-0">{{ $stats['needs_revision_submissions'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Submission Trends (Last 30 Days)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="submissionTrendsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Document Type Compliance</h5>
                        </div>
                        <div class="card-body">
                            @forelse($documentTypeCompliance ?? [] as $docType)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ $docType->name ?? 'Unknown' }}</span>
                                    <span class="badge bg-primary">{{ $docType->compliance_submissions_count ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $percentage = $docType->compliance_submissions_count ?? 0;
                                        $percentage = min($percentage, 100);
                                    @endphp
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                No compliance data available.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Performance -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Department Performance Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Total Submissions</th>
                                            <th>Approved</th>
                                            <th>Pending</th>
                                            <th>Compliance Rate</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($departmentPerformance ?? [] as $dept)
                                        <tr>
                                            <td>{{ $dept['name'] ?? 'Unknown' }}</td>
                                            <td>{{ $dept['total_submissions'] ?? 0 }}</td>
                                            <td>{{ $dept['approved_submissions'] ?? 0 }}</td>
                                            <td>{{ $dept['pending_submissions'] ?? 0 }}</td>
                                            <td>
                                                @php
                                                    $rate = $dept['compliance_rate'] ?? 0;
                                                    $badgeClass = $rate >= 80 ? 'bg-success' : ($rate >= 60 ? 'bg-warning' : 'bg-danger');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $rate }}%</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('reports.department') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No department data available.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Submission Trends Chart
const ctx = document.getElementById('submissionTrendsChart').getContext('2d');
const submissionTrendsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($submissionTrends->pluck('date') ?? []),
        datasets: [{
            label: 'Daily Submissions',
            data: @json($submissionTrends->pluck('count') ?? []),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
