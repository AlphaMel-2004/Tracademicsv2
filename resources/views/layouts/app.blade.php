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
        .status-needs-revision { background-color: #ffc107; color: #000; }
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

        .global-toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1085;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }

        .global-toast {
            min-width: 260px;
            max-width: 320px;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            color: #fff;
            font-size: 0.9rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: auto;
        }

        .global-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .global-toast-icon {
            font-size: 1.05rem;
            line-height: 1;
            margin-top: 0.1rem;
        }

        .global-toast-success { background: linear-gradient(135deg, #198754, #0f5132); }
        .global-toast-error { background: linear-gradient(135deg, #dc3545, #a71d2a); }
        .global-toast-warning { background: linear-gradient(135deg, #ffc107, #fd7e14); color: #212529 !important; }
        .global-toast-info { background: linear-gradient(135deg, #0dcaf0, #0b7285); }

        .mobile-sidebar-wrapper .nav-link {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mobile-sidebar-wrapper .nav-link:last-child {
            border-bottom: none;
        }

        .mobile-sidebar-wrapper .nav-link.active {
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand img {
                width: 20px !important;
                height: 20px !important;
            }

            main.py-4 {
                padding-top: 1.5rem !important;
            }

            .card {
                margin-bottom: 1rem;
            }

            .global-toast-container {
                top: 0.75rem;
                right: 0.75rem;
            }
        }
    </style>

    <script>
        (function() {
            const typeClassMap = {
                success: 'global-toast-success',
                error: 'global-toast-error',
                warning: 'global-toast-warning',
                info: 'global-toast-info'
            };

            const typeIconMap = {
                success: 'fas fa-check-circle',
                error: 'fas fa-times-circle',
                warning: 'fas fa-exclamation-circle',
                info: 'fas fa-info-circle'
            };

            function resolveContainer() {
                let container = document.querySelector('.global-toast-container');
                if (!container) {
                    if (!document.body) {
                        return null;
                    }
                    container = document.createElement('div');
                    container.className = 'global-toast-container';
                    document.body.appendChild(container);
                }
                return container;
            }

            window.showToast = function(message, type = 'info', options = {}) {
                if (!message) {
                    return;
                }

                if (!document.body) {
                    document.addEventListener('DOMContentLoaded', function() {
                        window.showToast(message, type, options);
                    }, { once: true });
                    return;
                }

                const duration = typeof options.duration === 'number' ? options.duration : 3000;
                const normalizedType = typeClassMap[type] ? type : 'info';

                const container = resolveContainer();
                if (!container) {
                    return;
                }

                const toastEl = document.createElement('div');
                toastEl.className = `global-toast ${typeClassMap[normalizedType]}`;

                const iconEl = document.createElement('span');
                iconEl.className = `global-toast-icon ${typeIconMap[normalizedType] ?? typeIconMap.info}`;

                const messageEl = document.createElement('div');
                messageEl.className = 'global-toast-message flex-grow-1';
                messageEl.textContent = typeof message === 'string' ? message : String(message);

                toastEl.appendChild(iconEl);
                toastEl.appendChild(messageEl);

                container.appendChild(toastEl);

                requestAnimationFrame(() => {
                    toastEl.classList.add('show');
                });

                const autoHide = setTimeout(() => {
                    toastEl.classList.remove('show');
                    toastEl.addEventListener('transitionend', () => {
                        toastEl.remove();
                    }, { once: true });
                }, duration);

                toastEl.addEventListener('click', () => {
                    clearTimeout(autoHide);
                    toastEl.classList.remove('show');
                    toastEl.addEventListener('transitionend', () => {
                        toastEl.remove();
                    }, { once: true });
                });
            };
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-user-role="{{ auth()->check() ? auth()->user()->role->name : 'Guest' }}">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/tracademics-logo.png') }}" alt="TracAdemics" class="me-2" style="height: 32px;">
                TracAdemics
            </a>

            @auth
            <div class="d-flex align-items-center ms-auto gap-2">
                <button class="btn btn-outline-primary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="navbar-nav">
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
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <div class="col-md-2 p-0 d-none d-md-block">
                <div class="sidebar p-3">
                    @include('layouts.partials.sidebar-links')
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-12 col-md-10">
            @else
            <div class="col-12">
            @endauth
                <!-- Page Content -->
                <main class="py-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @auth
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">
                <i class="fas fa-bars me-2"></i>Navigation
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="mobile-sidebar-wrapper p-3">
                @include('layouts.partials.sidebar-links')
            </div>
        </div>
    </div>
    @endauth

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

    @php
        $flashMessages = [
            'success' => session('success'),
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ];
    @endphp
    <script type="application/json" id="flash-messages-data">
        {!! json_encode($flashMessages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashDataEl = document.getElementById('flash-messages-data');
            let flashMessages = {};

            if (flashDataEl) {
                try {
                    flashMessages = JSON.parse(flashDataEl.textContent || '{}');
                } catch (error) {
                    flashMessages = {};
                }
            }

            Object.entries(flashMessages).forEach(function([type, message]) {
                if (message) {
                    showToast(message, type);
                }
            });

            const mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar) {
                mobileSidebar.addEventListener('click', function(event) {
                    const target = event.target.closest('.nav-link');
                    if (target) {
                        const offcanvasInstance = bootstrap.Offcanvas.getInstance(mobileSidebar);
                        if (offcanvasInstance) {
                            offcanvasInstance.hide();
                        }
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
