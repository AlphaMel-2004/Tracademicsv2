@extends('layouts.app')

@section('title', 'Add Program to ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add Program to {{ $department->name }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('programs-management.store', $department->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Program Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Program Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Program</button>
                        <a href="{{ route('programs-management.department', $department->id) }}" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
