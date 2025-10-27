@extends('layouts.app')

@section('title', 'Department Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-building me-2"></i>Department Management</h2>
                @if(in_array(Auth::user()->role->name, ['MIS', 'VPAA']))
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Department
                </a>
                @endif
            </div>
            <div class="row mb-4">
                <!-- Statistics Cards -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Departments</h6>
                                    <h3 class="mb-0">{{ $stats['total_departments'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-building fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Programs</h6>
                                    <h3 class="mb-0">{{ $stats['total_programs'] ?? 0 }}</h3>
                                </div>
                                                            <div class="align-self-center">
                                                                <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                                                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Faculty</h6>
                                    <h3 class="mb-0">{{ $stats['total_faculty'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-chalkboard-teacher fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Admin Staff</h6>
                                    <h3 class="mb-0">{{ $stats['total_staff'] ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-user-tie fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Departments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Department Name</th>
                                    <th>Code</th>
                                    <th>Programs</th>
                                    <th>Faculty/Staff</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $department)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $department->name }}</h6>
                                                <small class="text-muted">ID: {{ $department->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $department->code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $department->programs_count ?? 0 }} programs</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $department->users_count ?? 0 }} members</span>
                                    </td>
                                    <td>
                                        @if($department->description)
                                            {{ Str::limit($department->description, 50) }}
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('departments.programs', $department) }}" class="btn btn-sm btn-outline-info" title="Manage Programs">
                                                <i class="fas fa-graduation-cap"></i>
                                            </a>
                                            @if(in_array(Auth::user()->role->name, ['MIS', 'VPAA']))
                                            <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if(Auth::user()->role->name === 'MIS')
                                            <form method="POST" action="{{ route('departments.destroy', $department) }}" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this department?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-building fa-3x mb-3 d-block"></i>
                                        No departments found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($departments) && method_exists($departments, 'links'))
                        <div class="mt-3">
                            {{ $departments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
