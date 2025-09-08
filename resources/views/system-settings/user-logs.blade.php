@extends('layouts.app')

@section('title', 'User Logs - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">User Activity Logs</h1>
            <p class="text-muted">Monitor user activities and system access</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">System Settings</a></li>
                <li class="breadcrumb-item active">User Logs</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Logs</h5>
                            <h3 class="mb-0">{{ $userLogs->total() }}</h3>
                        </div>
                        <i class="fas fa-list-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Today's Activity</h5>
                            <h3 class="mb-0">{{ \App\Models\UserLog::whereDate('created_at', today())->count() }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Active Users</h5>
                            <h3 class="mb-0">{{ \App\Models\UserLog::distinct('user_id')->whereDate('created_at', today())->count('user_id') }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">This Week</h5>
                            <h3 class="mb-0">{{ \App\Models\UserLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</h3>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('settings.user-logs') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="user_filter" class="form-label">User</label>
                        <select name="user_id" id="user_filter" class="form-select">
                            <option value="">All Users</option>
                            @foreach(\App\Models\User::select('id', 'name')->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="action_filter" class="form-label">Action</label>
                        <select name="action" id="action_filter" class="form-select">
                            <option value="">All Actions</option>
                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('settings.user-logs') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- User Logs Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>
                Activity Logs
            </h5>
            <div class="text-muted small">
                Total: {{ $userLogs->total() }} logs
            </div>
        </div>
        <div class="card-body p-0">
            @if($userLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="15%">Timestamp</th>
                                <th width="25%">User Name</th>
                                <th width="12%">Role</th>
                                <th width="15%">Activity Type</th>
                                <th width="20%">Description</th>
                                <th width="8%">IP Address</th>
                                <th width="5%">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userLogs as $log)
                                <tr>
                                    <td>
                                        <div class="text-nowrap">
                                            <div><strong>{{ $log->created_at->format('M d, Y') }}</strong></div>
                                            <div class="text-muted small">{{ $log->created_at->format('h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 35px; height: 35px; font-size: 14px;">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-width-0">
                                                <div class="fw-bold text-truncate">{{ $log->user->name }}</div>
                                                <div class="text-muted small text-truncate">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $roleName = $log->user->role->name ?? 'Unknown';
                                            $badgeColor = match(strtolower($roleName)) {
                                                'admin' => 'danger',
                                                'mis' => 'warning',
                                                'faculty' => 'info',
                                                'vpaa' => 'success',
                                                'dean' => 'primary',
                                                'program head' => 'secondary',
                                                default => 'dark'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} text-nowrap">
                                            {{ strtoupper($roleName) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $actionColor = match(strtolower($log->action)) {
                                                'login' => 'success',
                                                'logout' => 'secondary',
                                                'create' => 'primary',
                                                'update' => 'info',
                                                'delete' => 'danger',
                                                'view' => 'light',
                                                default => 'primary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $actionColor }} text-nowrap">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-wrap">{{ $log->description }}</div>
                                    </td>
                                    <td>
                                        <code class="small text-nowrap">{{ $log->ip_address ?? 'N/A' }}</code>
                                    </td>
                                    <td class="text-center">
                                        @if($log->data)
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#logDetailsModal{{ $log->id }}" title="View Details">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Log Details Modal -->
                                @if($log->data)
                                <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-info-circle me-2"></i>Activity Details
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Activity Summary -->
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Activity Summary</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-2">
                                                                    <strong>User:</strong> {{ $log->user->name }}
                                                                </div>
                                                                <div class="mb-2">
                                                                    <strong>Email:</strong> {{ $log->user->email }}
                                                                </div>
                                                                <div class="mb-2">
                                                                    <strong>Role:</strong> 
                                                                    <span class="badge bg-info">{{ $log->user->role->name ?? 'Unknown' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-2">
                                                                    <strong>Action:</strong> 
                                                                    <span class="badge bg-primary">{{ ucfirst($log->action) }}</span>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <strong>Date & Time:</strong> {{ $log->created_at->format('M d, Y h:i A') }}
                                                                </div>
                                                                <div class="mb-2">
                                                                    <strong>IP Address:</strong> 
                                                                    <code>{{ $log->ip_address ?? 'N/A' }}</code>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>Description:</strong><br>
                                                            <div class="bg-light p-2 rounded">{{ $log->description }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Technical Details -->
                                                @if($log->data)
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Technical Details</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($log->user_agent)
                                                            <div class="mb-3">
                                                                <strong>User Agent:</strong><br>
                                                                <small class="text-muted">{{ $log->user_agent }}</small>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="mb-3">
                                                            <strong>Raw Data:</strong>
                                                            <div class="bg-dark text-light p-3 rounded mt-2" style="font-family: 'Courier New', monospace; font-size: 12px; max-height: 300px; overflow-y: auto;">
                                                                <pre class="text-light mb-0">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No user activity logs found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($userLogs->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $userLogs->links() }}
        </div>
    @endif
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: bold;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.text-nowrap {
    white-space: nowrap;
}

.text-wrap {
    word-wrap: break-word;
    max-width: 200px;
}

.min-width-0 {
    min-width: 0;
    flex: 1;
}

.badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.5rem;
}

.table-responsive {
    border-radius: 0.375rem;
}

.modal-dialog {
    max-width: 900px;
}

pre {
    white-space: pre-wrap;
    word-wrap: break-word;
}

.bg-dark pre {
    color: #f8f9fa !important;
    margin: 0;
}

/* Ensure proper column widths */
.table th:nth-child(1), .table td:nth-child(1) { width: 15%; }
.table th:nth-child(2), .table td:nth-child(2) { width: 25%; }
.table th:nth-child(3), .table td:nth-child(3) { width: 12%; }
.table th:nth-child(4), .table td:nth-child(4) { width: 15%; }
.table th:nth-child(5), .table td:nth-child(5) { width: 20%; }
.table th:nth-child(6), .table td:nth-child(6) { width: 8%; }
.table th:nth-child(7), .table td:nth-child(7) { width: 5%; }

/* Responsive improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .avatar-sm {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endsection
