<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TracAdemics') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/tracademics-logo.png') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

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
            min-height: calc(100vh - 50px);
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
        
        /* Compact navbar styling */
        .navbar {
            padding: 0.25rem 1rem;
            min-height: 50px;
        }
        
        .navbar-brand {
            padding: 0.25rem 0;
            margin-right: 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            width: 24px !important;
            height: 24px !important;
        }
        
        .navbar-nav .nav-link {
            padding: 0.375rem 0.75rem;
        }
        
        .dropdown-toggle::after {
            margin-left: 0.5rem;
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
        
        /* Enhanced Faculty Dashboard Styles */
        .faculty-dashboard .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .faculty-dashboard .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .progress-bar {
            transition: width 0.3s ease-in-out;
        }
        
        .badge-sm {
            font-size: 0.65rem;
        }
        
        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--primary-green), #20c997) !important;
        }
        
        .card-header.bg-success {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
        }
        
        .card-header.bg-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
        }
        
        .list-group-item {
            border-left: 0;
            border-right: 0;
        }
        
        .list-group-item:first-child {
            border-top: 0;
        }
        
        .list-group-item:last-child {
            border-bottom: 0;
        }
        
        .opacity-50 {
            opacity: 0.5;
        }
        
        .h-100 {
            height: 100% !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/tracademics-logo.png') }}" alt="TracAdemics" class="me-2" style="height: 32px;">
                TracAdemics
            </a>
            
            @auth
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ Auth::user()->name }}
                        <span class="badge bg-primary ms-2">{{ Auth::user()->role->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.password') }}"><i class="fas fa-lock me-2"></i>Password Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
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
                        
                        @if(Auth::user()->role->name === 'VPAA')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('monitor.*') ? 'active' : '' }}" href="{{ route('monitor.index') }}">
                                <i class="fas fa-monitor me-2"></i>Monitor
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Dean')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('monitor.faculty.*') ? 'active' : '' }}" href="{{ route('monitor.faculty') }}">
                                <i class="fas fa-users me-2"></i>Monitor Faculty
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.dean') }}">
                                <i class="fas fa-file-pdf me-2"></i>Reports
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Program Head')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('monitor.compliance.*') ? 'active' : '' }}" href="{{ route('monitor.compliance') }}">
                                <i class="fas fa-clipboard-check me-2"></i>Monitor Compliances
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                <i class="fas fa-book me-2"></i>Subject Management
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Faculty')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subjects.assigned') ? 'active' : '' }}" href="{{ route('subjects.assigned') }}">
                                <i class="fas fa-book-open me-2"></i>Subjects
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
                            <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                <i class="fas fa-building me-2"></i>Department Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('programs-management.*') ? 'active' : '' }}" href="{{ route('programs-management.index') }}">
                                <i class="fas fa-graduation-cap me-2"></i>Programs Management
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

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to logout from TracAdemics?</p>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        You will need to login again to access your account.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
