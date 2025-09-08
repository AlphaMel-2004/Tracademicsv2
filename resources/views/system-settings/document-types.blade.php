@extends('layouts.app')

@section('title', 'Document Types - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Document Types Management</h1>
            <p class="text-muted">Manage document types used in the compliance system</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">System Settings</a></li>
                <li class="breadcrumb-item active">Document Types</li>
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
                            <h5 class="card-title mb-1">Total Types</h5>
                            <h3 class="mb-0">{{ $documentTypes->count() }}</h3>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Active Types</h5>
                            <h3 class="mb-0">{{ $documentTypes->where('is_active', true)->count() }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Required Types</h5>
                            <h3 class="mb-0">{{ $documentTypes->where('is_required', true)->count() }}</h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Templates</h5>
                            <h3 class="mb-0">{{ $documentTypes->whereNotNull('template_path')->count() }}</h3>
                        </div>
                        <i class="fas fa-file-download fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Document Type Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>
                Add New Document Type
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.document-types.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., Syllabus, Lesson Plan" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code') }}" placeholder="e.g., SYL, LP" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="template" class="form-label">Template File</label>
                            <input type="file" name="template" id="template" class="form-control @error('template') is-invalid @enderror" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx">
                            @error('template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" {{ old('is_required') ? 'checked' : '' }}>
                            <label for="is_required" class="form-check-label">
                                Required Document
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Add Document Type
                </button>
            </form>
        </div>
    </div>

    <!-- Document Types Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Document Types
            </h5>
        </div>
        <div class="card-body p-0">
            @if($documentTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Required</th>
                                <th>Template</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentTypes as $type)
                                <tr>
                                    <td>
                                        <strong>{{ $type->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $type->code }}</span>
                                    </td>
                                    <td>{{ $type->description ?? 'No description' }}</td>
                                    <td>
                                        @if($type->is_required)
                                            <span class="badge bg-warning">Required</span>
                                        @else
                                            <span class="badge bg-secondary">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->template_path)
                                            <a href="{{ asset('storage/' . $type->template_path) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No template</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $type->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $type->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Document Type</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('settings.document-types.update', $type) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="edit_name_{{ $type->id }}" class="form-label">Name</label>
                                                        <input type="text" name="name" id="edit_name_{{ $type->id }}" 
                                                               class="form-control" value="{{ $type->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_code_{{ $type->id }}" class="form-label">Code</label>
                                                        <input type="text" name="code" id="edit_code_{{ $type->id }}" 
                                                               class="form-control" value="{{ $type->code }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_description_{{ $type->id }}" class="form-label">Description</label>
                                                        <textarea name="description" id="edit_description_{{ $type->id }}" 
                                                                  class="form-control" rows="3">{{ $type->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_template_{{ $type->id }}" class="form-label">Template File</label>
                                                        <input type="file" name="template" id="edit_template_{{ $type->id }}" 
                                                               class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                        @if($type->template_path)
                                                            <small class="text-muted">Current: {{ basename($type->template_path) }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input type="checkbox" name="is_required" id="edit_is_required_{{ $type->id }}" 
                                                               class="form-check-input" value="1" {{ $type->is_required ? 'checked' : '' }}>
                                                        <label for="edit_is_required_{{ $type->id }}" class="form-check-label">Required Document</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="is_active" id="edit_is_active_{{ $type->id }}" 
                                                               class="form-check-input" value="1" {{ $type->is_active ? 'checked' : '' }}>
                                                        <label for="edit_is_active_{{ $type->id }}" class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $type->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Document Type</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the document type "<strong>{{ $type->name }}</strong>"?</p>
                                                <p class="text-danger"><small>This action cannot be undone.</small></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('settings.document-types.destroy', $type) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Document Types Found</h5>
                    <p class="text-muted">Add your first document type using the form above.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
