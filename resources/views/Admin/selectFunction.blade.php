@php
$routePrefix = function($route) {
    $role = strtolower(Auth::user()->role);
    if ($role === 'admin') {
        return 'admin.' . $route;
    } elseif ($role === 'pka') {
        return 'pka.' . $route;
    }
    return 'admin.' . $route; // fallback
};
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Select File Function – {{ config('app.name') }}</title>
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        #accordionSidebar {
            background-color: #007b78 !important;
            background-image: none !important;
        }
        .sidebar-brand-text { color: white; }
        .container-fluid {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body id="page-top">
  <div id="wrapper">
    {{-- Sidebar --}}
    <ul id="accordionSidebar" class="navbar-nav sidebar sidebar-dark accordion">
        <!-- Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-start pl-3" href="{{ route($routePrefix('dashboard')) }}">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('images/jupem-logo.jpg') }}" alt="Jupem Logo" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            </div>
            <div class="sidebar-brand-text mx-3">{{ Auth::user()->roleDisplayName ?? 'Admin' }}</div>
        </a>
        <hr class="sidebar-divider my-0">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <!-- Function Selection -->
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('admin.selectFunction') }}">
                <i class="fas fa-th-large"></i>
                <span>Select File Function</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <!-- Components -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents"
            aria-expanded="true" aria-controls="collapseComponents">
            <i class="fas fa-fw fa-folder"></i>
            <span>Manage Files</span>
          </a>
          <div id="collapseComponents" class="collapse" aria-labelledby="headingComponents"
              data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">View File</h6>
              <a class="collapse-item" href="{{ route('admin.manageFiles') }}">File List</a>
              <a class="collapse-item" href="{{ route('admin.ffaddFile') }}">Add File</a>
            </div>
          </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <!--Manage Request Dropdown -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRequest"
             aria-expanded="true" aria-controls="collapseRequest">
            <i class="fas fa-tasks"></i>
            <span>Pengurusan Permohonan</span>
          </a>
          <div id="collapseRequest" class="collapse" aria-labelledby="headingRequest"
               data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Operasi Permohonan:</h6>
              <a class="collapse-item" href="{{ route('admin.manageRequest') }}">Permohonan Fail</a>
              <a class="collapse-item" href="{{ route('admin.requestStatus') }}">Status Tempahan</a>
            </div>
          </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <!-- Track History -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.requestHistory') }}">
                <i class="fas fa-history"></i>
                <span>Track Request History</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <!-- Manage User -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.manageUser') }}">
                <i class="fas fa-users-cog"></i>
                <span>Manage Users</span>
            </a>
        </li>
        <!-- Sidebar Toggler -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <!-- User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->username ?? 'Admin' }}</span>
                            @if(Auth::user() && Auth::user()->profilePicture)
                                <img class="img-profile rounded-circle" src="{{ asset('images/' . Auth::user()->profilePicture) }}" style="width: 30px; height: 30px; object-fit: cover;">
                            @else
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                            @endif
                        </a>
                        <!-- Dropdown – User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('admin.manageProfile') }}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Log Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->
            <div class="container-fluid">
                <h2 class="mb-4">{{ __('messages.select_file_function') }}</h2>
                <div class="row">
                    @foreach($functions as $function)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title">{{ $function['slug'] }}</h4>
                                <h6 class="card-subtitle mb-2 text-muted" style="font-size: 1rem;">{{ $function['name'] }}</h6>
                                <p class="card-text flex-grow-1">{{ $function['description'] }}</p>
                                <a href="{{ route('admin.manageFiles', ['function' => $function['slug']]) }}" class="btn btn-primary mt-auto">{{ __('messages.view_files') }}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Hak Cipta &copy; {{ config('app.name') }} {{ date('Y') }}</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
  </div>
  <!-- End of Page Wrapper -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>
