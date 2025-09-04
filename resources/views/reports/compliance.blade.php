@extends('layouts.app')

@section('title', 'Compliance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2><i class="fas fa-file-alt me-2"></i>Compliance Report</h2>
            <p class="text-muted">Detailed compliance tracking and submission analytics will be displayed here.</p>
            
            <div class="card">
                <div class="card-body">
                    <p>Compliance Report functionality is under development.</p>
                    <a href="{{ route('reports.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Reports Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
