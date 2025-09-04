@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Welcome, {{ $user->name }}</h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-primary me-2">{{ $user->role->name }}</span>
                        @if($user->department)
                            {{ $user->department->name }}
                        @endif
                        @if($dashboardData['active_semester'] ?? null)
                            • {{ $dashboardData['active_semester']->name }} {{ $dashboardData['active_semester']->academic_year }}
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <div class="small text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('F j, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-specific Dashboard Content -->
    @if($user->role->name === 'MIS')
        @include('dashboard.mis')
    @elseif($user->role->name === 'VPAA')
        @include('dashboard.vpaa')
    @elseif($user->role->name === 'Dean')
        @include('dashboard.dean')
    @elseif($user->role->name === 'Program Head')
        @include('dashboard.program-head')
    @elseif($user->role->name === 'Faculty')
        @include('dashboard.faculty')
    @endif
</div>
@endsection
