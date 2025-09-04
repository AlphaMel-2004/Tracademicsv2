@extends('layouts.app')

@section('title', 'Department Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-building me-2"></i>Department Performance Report</h2>
            <p class="text-muted">Detailed department compliance and performance metrics will be displayed here.</p>
            
            <div class="card">
                <div class="card-body">
                    <p>Department Report functionality is under development.</p>
                    <a href="{{ route('reports.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Reports Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
