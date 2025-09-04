@extends('layouts.app')

@section('title', 'Faculty Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users-cog me-2"></i>Faculty Management</h2>
                <a href="{{ route('faculty.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Faculty
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row mb-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Faculty</h6>
                                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Faculty</h6>
                                    <h3 class="mb-0">{{ $stats['active'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-check fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Pending Compliance</h6>
                                    <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Subjects Assigned</h6>
                                    <h3 class="mb-0">{{ $stats['subjects'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faculty List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Faculty List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Subjects Assigned</th>
                                    <th>Compliance Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculty as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $member->name }}</h6>
                                                <small class="text-muted">Faculty ID: {{ $member->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->department->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $member->subjects_count ?? 0 }} subjects</span>
                                    </td>
                                    <td>
                                        @php
                                            $complianceRate = $member->compliance_rate ?? 0;
                                        @endphp
                                        @if($complianceRate >= 80)
                                            <span class="badge bg-success">Excellent ({{ $complianceRate }}%)</span>
                                        @elseif($complianceRate >= 60)
                                            <span class="badge bg-warning">Good ({{ $complianceRate }}%)</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement ({{ $complianceRate }}%)</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->last_login_at)
                                            {{ $member->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('faculty.show', $member) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('faculty.edit', $member) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('faculty.assignments', $member) }}" class="btn btn-sm btn-outline-info" title="Manage Assignments">
                                                <i class="fas fa-tasks"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                        No faculty members found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($faculty) && method_exists($faculty, 'links'))
                        <div class="mt-3">
                            {{ $faculty->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Faculty Activities</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                    <p class="mb-1 text-muted">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                                <span class="badge bg-{{ $activity['type'] ?? 'primary' }}">{{ $activity['status'] ?? 'New' }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-2 d-block"></i>
                            No recent activities.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
