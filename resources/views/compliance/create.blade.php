@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-upload me-2"></i>
                        Submit: {{ $documentType->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Document Type Information -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Document Information</h6>
                        <p class="mb-1"><strong>Type:</strong> {{ $documentType->name }}</p>
                        @if($documentType->description)
                            <p class="mb-1"><strong>Description:</strong> {{ $documentType->description }}</p>
                        @endif
                        <p class="mb-1"><strong>Submission Type:</strong> 
                            <span class="badge bg-secondary">{{ ucfirst($documentType->submission_type) }}</span>
                        </p>
                        @php
                            $currentSemester = \App\Models\Semester::where('is_active', true)->first();
                            $deadline = $currentSemester ? \Carbon\Carbon::parse($currentSemester->start_date)->addDays($documentType->due_days) : null;
                        @endphp
                        @if($deadline)
                            <p class="mb-0"><strong>Deadline:</strong> 
                                <span class="text-danger">{{ $deadline->format('M d, Y') }}</span>
                            </p>
                        @endif
                    </div>

                    <!-- Submission Form -->
                    <form action="{{ route('compliance.store') }}" method="POST" enctype="multipart/form-data" id="submissionForm">
                        @csrf
                        <input type="hidden" name="document_type_id" value="{{ $documentType->id }}">

                        <!-- Subject Selection (if required) -->
                        @if($documentType->submission_type === 'subject' && $subjects->count() > 0)
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">
                                    <i class="fas fa-book me-1"></i>Select Subject <span class="text-danger">*</span>
                                </label>
                                <select name="subject_id" id="subject_id" class="form-select" required>
                                    <option value="">Choose a subject...</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->code }} - {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($documentType->submission_type === 'subject' && $subjects->count() === 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                You are not assigned to any subjects for the current semester. Please contact your administrator.
                            </div>
                        @endif

                        <!-- File Upload Section -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-file-upload me-1"></i>Upload Documents
                            </label>
                            <div class="border-dashed p-3 rounded">
                                <div class="text-center mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Drag and drop files here or click to browse</p>
                                    <small class="text-muted">
                                        Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB each)
                                    </small>
                                </div>
                                <input type="file" name="files[]" id="fileInput" class="form-control" multiple 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div id="filePreview" class="mt-3"></div>
                            </div>
                        </div>

                        <!-- Links Section -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-link me-1"></i>Add Links (Optional)
                            </label>
                            <div id="linksContainer">
                                <div class="link-item border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="url" name="links[0][url]" class="form-control" 
                                                   placeholder="https://example.com">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="links[0][title]" class="form-control" 
                                                   placeholder="Link title">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-link" 
                                                    style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <textarea name="links[0][description]" class="form-control" rows="2" 
                                                    placeholder="Link description (optional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addLink" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Add Another Link
                            </button>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i>Submit Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-dashed {
    border: 2px dashed #dee2e6;
    transition: border-color 0.3s ease;
}

.border-dashed:hover {
    border-color: #007bff;
}

.file-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

.link-item {
    background: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let linkIndex = 1;

    // File preview functionality
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const filePreview = document.getElementById('filePreview');
        filePreview.innerHTML = '';

        Array.from(e.target.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center';
            fileItem.innerHTML = `
                <div>
                    <i class="fas fa-file-alt me-2"></i>
                    <span>${file.name}</span>
                    <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            filePreview.appendChild(fileItem);
        });

        // Add remove file functionality
        document.querySelectorAll('.remove-file').forEach(btn => {
            btn.addEventListener('click', function() {
                // Note: This is a simplified approach. In production, you'd want to handle file removal more elegantly
                this.closest('.file-item').remove();
            });
        });
    });

    // Add link functionality
    document.getElementById('addLink').addEventListener('click', function() {
        const linksContainer = document.getElementById('linksContainer');
        const linkItem = document.createElement('div');
        linkItem.className = 'link-item border rounded p-3 mb-2';
        linkItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <input type="url" name="links[${linkIndex}][url]" class="form-control" 
                           placeholder="https://example.com">
                </div>
                <div class="col-md-4">
                    <input type="text" name="links[${linkIndex}][title]" class="form-control" 
                           placeholder="Link title">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-link">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <textarea name="links[${linkIndex}][description]" class="form-control" rows="2" 
                            placeholder="Link description (optional)"></textarea>
                </div>
            </div>
        `;
        linksContainer.appendChild(linkItem);
        linkIndex++;
        updateRemoveButtons();
    });

    // Remove link functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-link')) {
            e.target.closest('.link-item').remove();
            updateRemoveButtons();
        }
    });

    // Update remove button visibility
    function updateRemoveButtons() {
        const linkItems = document.querySelectorAll('.link-item');
        linkItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-link');
            if (linkItems.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // Form submission validation
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        const files = document.getElementById('fileInput').files;
        const firstLinkUrl = document.querySelector('input[name="links[0][url]"]').value;
        
        if (files.length === 0 && !firstLinkUrl) {
            e.preventDefault();
            alert('Please upload at least one file or provide a link.');
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
    });
});
</script>
@endsection
