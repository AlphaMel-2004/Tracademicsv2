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
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userLogs as $log)
                                <tr>
                                    <td>
                                        <div class="small">
                                            <strong>{{ $log->created_at->format('M d, Y') }}</strong><br>
                                            <span class="text-muted">{{ $log->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $log->user->name }}</strong><br>
                                                <small class="text-muted">{{ $log->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->action == 'login' ? 'success' : ($log->action == 'logout' ? 'secondary' : ($log->action == 'delete' ? 'danger' : 'primary')) }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <code class="small">{{ $log->ip_address ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        @if($log->data)
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#logDetailsModal{{ $log->id }}">
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
                                                <h5 class="modal-title">Log Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>User:</strong> {{ $log->user->name }}<br>
                                                        <strong>Action:</strong> {{ ucfirst($log->action) }}<br>
                                                        <strong>Time:</strong> {{ $log->created_at->format('M d, Y h:i A') }}<br>
                                                        <strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>User Agent:</strong><br>
                                                        <small class="text-muted">{{ $log->user_agent ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                                <hr>
                                                <strong>Additional Data:</strong>
                                                <pre class="bg-light p-3 rounded mt-2"><code>{{ json_encode($log->data, JSON_PRETTY_PRINT) }}</code></pre>
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
</style>
@endsection
