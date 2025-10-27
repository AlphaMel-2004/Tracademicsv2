@php
    $listClass = $listClass ?? 'nav flex-column';
@endphp

<ul class="{{ $listClass }}">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
    </li>

    @if(Auth::check() && Auth::user()->role->name === 'VPAA')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('monitor.*') ? 'active' : '' }}" href="{{ route('monitor.index') }}">
                <i class="fas fa-monitor me-2"></i>Monitor
            </a>
        </li>
    @endif

    @if(Auth::check() && Auth::user()->role->name === 'Dean')
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

    @if(Auth::check() && Auth::user()->role->name === 'Program Head')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('monitor.compliance.*') ? 'active' : '' }}" href="{{ route('monitor.compliance') }}">
                <i class="fas fa-clipboard-check me-2"></i>Monitor Compliances
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('faculty.manage.*') ? 'active' : '' }}" href="{{ route('faculty.manage') }}">
                <i class="fas fa-users-cog me-2"></i>Manage Faculty
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                <i class="fas fa-book me-2"></i>Subject Management
            </a>
        </li>
    @endif

    @if(Auth::check() && Auth::user()->role->name === 'Faculty')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('subjects.assigned') ? 'active' : '' }}" href="{{ route('subjects.assigned') }}">
                <i class="fas fa-book-open me-2"></i>Subjects
            </a>
        </li>
    @endif

    @if(Auth::check() && Auth::user()->role->name === 'MIS')
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
            <a class="nav-link {{ request()->routeIs('settings.semesters*') ? 'active' : '' }}" href="{{ route('settings.semesters') }}">
                <i class="fas fa-calendar-alt me-2"></i>Semester Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') && !request()->routeIs('settings.semesters*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <i class="fas fa-cogs me-2"></i>System Settings
            </a>
        </li>
    @endif
</ul>
