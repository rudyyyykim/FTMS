<!-- Navigation sidebar component -->
<nav class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start pl-3" href="{{ route($routePrefix.'dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('images/jupem-logo.jpg') }}" alt="Jupem Logo" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
        </div>
        <div class="sidebar-brand-text mx-3">{{ Auth::user()->roleDisplayName ?? 'Admin' }}</div>
    </a>

    <hr class="sidebar-divider my-0">

    @php
        $isAdmin = Auth::user()->role == 'Admin';
        $isPka = Auth::user()->role == 'Pka';
        $routePrefix = $isPka ? 'pka.' : 'admin.';
    @endphp

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs($routePrefix.'dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($routePrefix.'dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Components - Only visible to Admin -->
    @if($isAdmin)
    <li class="nav-item {{ request()->routeIs('admin.manageFiles') || request()->routeIs('admin.ffaddFile') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents"
           aria-expanded="true" aria-controls="collapseComponents">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pengurusan Fail</span>
        </a>
        <div id="collapseComponents" class="collapse {{ request()->routeIs('admin.manageFiles') || request()->routeIs('admin.ffaddFile') ? 'show' : '' }}" aria-labelledby="headingComponents" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Operasi Fail:</h6>
                <a class="collapse-item {{ request()->routeIs('admin.manageFiles') ? 'active' : '' }}" href="{{ route('admin.manageFiles') }}">Senarai Fail</a>
                <a class="collapse-item {{ request()->routeIs('admin.ffaddFile') ? 'active' : '' }}" href="{{ route('admin.ffaddFile') }}">Tambah Fail</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    @endif

    <!--Manage Request Dropdown -->
    <li class="nav-item {{ request()->routeIs($routePrefix.'manageRequest') || request()->routeIs($routePrefix.'requestStatus') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRequest"
           aria-expanded="true" aria-controls="collapseRequest">
            <i class="fas fa-tasks"></i>
            <span>Pengurusan Permohonan</span>
        </a>
        <div id="collapseRequest" class="collapse {{ request()->routeIs($routePrefix.'manageRequest') || request()->routeIs($routePrefix.'requestStatus') ? 'show' : '' }}" aria-labelledby="headingRequest" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Operasi Permohonan:</h6>
                <a class="collapse-item {{ request()->routeIs($routePrefix.'manageRequest') ? 'active' : '' }}" href="{{ route($routePrefix.'manageRequest') }}">Permohonan Fail</a>
                <a class="collapse-item {{ request()->routeIs($routePrefix.'requestStatus') ? 'active' : '' }}" href="{{ route($routePrefix.'requestStatus') }}">Status Tempahan</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!--Manage Return -->
    <li class="nav-item {{ request()->routeIs($routePrefix.'manageReturn') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($routePrefix.'manageReturn') }}">
            <i class="fas fa-file-export"></i>
            <span>Pemulangan Fail</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!--Track History -->
    <li class="nav-item {{ request()->routeIs($routePrefix.'requestHistory') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($routePrefix.'requestHistory') }}">
            <i class="fas fa-history"></i>
            <span>Sejarah Permohonan</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!--Manage User - Only visible to Admin -->
    @if($isAdmin)
    <li class="nav-item {{ request()->routeIs('admin.manageUser') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.manageUser') }}">
            <i class="fas fa-users-cog"></i>
            <span>Urus pengguna</span>
        </a>
    </li>
    @endif

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</nav>
