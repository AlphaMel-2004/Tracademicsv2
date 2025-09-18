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
                            <h5 class="card-title mb-1">Required</h5>
                            <h3 class="mb-0">{{ $documentTypes->where('is_required', true)->count() }}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Semester-wide</h5>
                            <h3 class="mb-0">{{ $documentTypes->where('submission_type', 'semester')->count() }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Subject-specific</h5>
                            <h3 class="mb-0">{{ $documentTypes->where('submission_type', 'subject')->count() }}</h3>
                        </div>
                        <i class="fas fa-book fa-2x opacity-75"></i>
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., Course Syllabus, Final Test Questions" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="submission_type" class="form-label">Submission Type <span class="text-danger">*</span></label>
                            <select name="submission_type" id="submission_type" class="form-select @error('submission_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="semester" {{ old('submission_type') === 'semester' ? 'selected' : '' }}>
                                    Semester-wide (Submit once per semester)
                                </option>
                                <option value="subject" {{ old('submission_type') === 'subject' ? 'selected' : '' }}>
                                    Subject-specific (Submit per subject)
                                </option>
                            </select>
                            @error('submission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="due_days" class="form-label">Due Days <span class="text-danger">*</span></label>
                            <input type="number" name="due_days" id="due_days" class="form-control @error('due_days') is-invalid @enderror" 
                                   value="{{ old('due_days', 30) }}" min="1" max="180" required>
                            <small class="form-text text-muted">Days from semester start</small>
                            @error('due_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Enter detailed description of the document requirements and content">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" {{ old('is_required', true) ? 'checked' : '' }}>
                            <label for="is_required" class="form-check-label">
                                <strong>Required Document</strong>
                                <small class="text-muted d-block">Faculty must submit this document for compliance</small>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Document Type
                        </button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-undo me-2"></i>Reset Form
                        </button>
                    </div>
                    <div class="text-muted">
                        <small><i class="fas fa-info-circle me-1"></i>Document types define what faculty must submit for compliance tracking</small>
                    </div>
                </div>
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
                                <th>Submission Type</th>
                                <th>Description</th>
                                <th>Required</th>
                                <th>Due Days</th>
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
                                        @if($type->submission_type === 'semester')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-calendar-alt me-1"></i>Semester-wide
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-book me-1"></i>Subject-specific
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($type->description ?? 'No description', 60) }}</small>
                                    </td>
                                    <td>
                                        @if($type->is_required)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Required
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $type->due_days ?? 30 }} days</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
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
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-edit me-2"></i>Edit Document Type
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('settings.document-types.update', $type) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_name_{{ $type->id }}" class="form-label">
                                                                    Document Type Name <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" name="name" id="edit_name_{{ $type->id }}" 
                                                                       class="form-control" value="{{ $type->name }}" 
                                                                       placeholder="e.g., Course Syllabus, Final Test Questions" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_submission_type_{{ $type->id }}" class="form-label">
                                                                    Submission Type <span class="text-danger">*</span>
                                                                </label>
                                                                <select name="submission_type" id="edit_submission_type_{{ $type->id }}" 
                                                                        class="form-select" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="semester" {{ $type->submission_type === 'semester' ? 'selected' : '' }}>
                                                                        Semester-wide (Submit once per semester)
                                                                    </option>
                                                                    <option value="subject" {{ $type->submission_type === 'subject' ? 'selected' : '' }}>
                                                                        Subject-specific (Submit per subject)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="edit_description_{{ $type->id }}" class="form-label">Description</label>
                                                                <textarea name="description" id="edit_description_{{ $type->id }}" 
                                                                          class="form-control" rows="3" 
                                                                          placeholder="Enter detailed description of the document requirements and content">{{ $type->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_due_days_{{ $type->id }}" class="form-label">
                                                                    Due Days <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="number" name="due_days" id="edit_due_days_{{ $type->id }}" 
                                                                       class="form-control" value="{{ $type->due_days ?? 30 }}" 
                                                                       min="1" max="180" required>
                                                                <small class="form-text text-muted">Days from semester start</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Document Settings</label>
                                                                <div class="form-check mt-2">
                                                                    <input type="checkbox" name="is_required" id="edit_is_required_{{ $type->id }}" 
                                                                           class="form-check-input" value="1" {{ $type->is_required ? 'checked' : '' }}>
                                                                    <label for="edit_is_required_{{ $type->id }}" class="form-check-label">
                                                                        <strong>Required Document</strong>
                                                                        <small class="text-muted d-block">Faculty must submit this document for compliance</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-2"></i>Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-2"></i>Update Document Type
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $type->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-trash me-2"></i>Delete Document Type
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                                </div>
                                                <p class="text-center">Are you sure you want to delete the document type:</p>
                                                <div class="alert alert-light text-center">
                                                    <strong>"{{ $type->name }}"</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($type->submission_type === 'semester')
                                                            <i class="fas fa-calendar-alt me-1"></i>Semester-wide
                                                        @else
                                                            <i class="fas fa-book me-1"></i>Subject-specific
                                                        @endif
                                                        | 
                                                        @if($type->is_required)
                                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>Required
                                                        @else
                                                            Optional
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="text-danger text-center">
                                                    <i class="fas fa-warning me-1"></i>
                                                    <small>This action cannot be undone and may affect existing compliance records.</small>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i>Cancel
                                                </button>
                                                <form action="{{ route('settings.document-types.destroy', $type) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash me-2"></i>Delete Document Type
                                                    </button>
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
