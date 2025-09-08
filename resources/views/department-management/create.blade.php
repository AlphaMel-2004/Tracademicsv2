@extends('layouts.app')

@section('title', 'Add Department - TracAdemics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Department</h1>
            <p class="text-muted">Create a new department in the system</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Department Management</a></li>
                <li class="breadcrumb-item active">Add Department</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Department Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" placeholder="Enter department name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code') }}" placeholder="e.g., CSE, ME, ECE" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" placeholder="Enter department description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="head_of_department" class="form-label">Head of Department</label>
                                    <input type="text" name="head_of_department" id="head_of_department" 
                                           class="form-control @error('head_of_department') is-invalid @enderror" 
                                           value="{{ old('head_of_department') }}" placeholder="Enter HOD name">
                                    @error('head_of_department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" id="contact_email" 
                                           class="form-control @error('contact_email') is-invalid @enderror" 
                                           value="{{ old('contact_email') }}" placeholder="Enter contact email">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" id="contact_phone" 
                                           class="form-control @error('contact_phone') is-invalid @enderror" 
                                           value="{{ old('contact_phone') }}" placeholder="Enter contact phone">
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location') }}" placeholder="Enter department location">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    Active Department
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
