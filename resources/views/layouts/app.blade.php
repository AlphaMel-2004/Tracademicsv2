<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TracAdemics') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #28a745;
            --secondary-blue: #007bff;
            --light-bg: #f8f9fa;
            --dark-text: #343a40;
        }
        
        .navbar-brand {
            color: var(--primary-green) !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .card-header {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--primary-green);
        }
        
        .sidebar {
            background-color: var(--light-bg);
            min-height: calc(100vh - 56px);
        }
        
        .sidebar .nav-link {
            color: var(--dark-text);
            border-radius: 0.5rem;
            margin: 0.2rem 0;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--primary-green);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-green);
            color: white;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
        
        .status-pending { background-color: #ffc107; color: #000; }
        .status-submitted { background-color: #17a2b8; color: #fff; }
        .status-approved { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
        .status-under-review { background-color: #6f42c1; color: #fff; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                TracAdemics
            </a>
            
            @auth
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ Auth::user()->name }}
                        <span class="badge bg-primary ms-1">{{ Auth::user()->role->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        
                        @if(Auth::user()->role->name === 'Faculty')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('compliance.create') ? 'active' : '' }}" href="{{ route('compliance.create') }}">
                                <i class="fas fa-upload me-2"></i>Submit Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('compliance.my-submissions') ? 'active' : '' }}" href="{{ route('compliance.my-submissions') }}">
                                <i class="fas fa-list me-2"></i>My Submissions
                            </a>
                        </li>
                        @endif
                        
                        @if(in_array(Auth::user()->role->name, ['MIS', 'VPAA', 'Dean', 'Program Head']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.dashboard') }}">
                                <i class="fas fa-chart-bar me-2"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('compliance.review') ? 'active' : '' }}" href="{{ route('compliance.review') }}">
                                <i class="fas fa-eye me-2"></i>Review Submissions
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Program Head')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('faculty.*') ? 'active' : '' }}" href="{{ route('faculty.index') }}">
                                <i class="fas fa-users-cog me-2"></i>Faculty Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                <i class="fas fa-book me-2"></i>Subject Management
                            </a>
                        </li>
                        @endif
                        
                        @if(in_array(Auth::user()->role->name, ['MIS', 'VPAA', 'Dean']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                <i class="fas fa-building me-2"></i>Department Management
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->role->name === 'MIS')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users me-2"></i>User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                <i class="fas fa-cogs me-2"></i>System Settings
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-md-10">
                @endif
                
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Page Content -->
                <main class="py-4">
                    @yield('content')
                </main>
                
                @auth
            </div>
            @endauth
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
