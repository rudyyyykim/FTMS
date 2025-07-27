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
    <title>Permohonan Fail – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Inline Custom Sidebar Styles -->
    <style>
        /* Custom sidebar background color and no gradient */
        #accordionSidebar {
            background-color: #007b78 !important; /* Custom background */
            background-image: none !important; /* Remove gradient */
        }

        /* Customize the sidebar brand text and icon */
        .sidebar-brand-text {
            color: white; /* Make the text visible */
        }

        .btn-sidebar {
            background-color: #007b78 !important;
            border-color: #007b78 !important;
            color: #fff !important;
        }
        .btn-sidebar:hover, .btn-sidebar:focus {
            background-color: #005f56 !important;
            border-color: #005f56 !important;
            color: #fff !important;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul id="accordionSidebar"
      class="navbar-nav sidebar sidebar-dark accordion">
      <!-- Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-start pl-3"
        href="{{ route($routePrefix('dashboard')) }}">
        <div class="sidebar-brand-icon">
          <img
            src="{{ asset('images/jupem-logo.jpg') }}"
            alt="Jupem Logo"
            style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
        </div>
        <div class="sidebar-brand-text mx-3">{{ Auth::user()->roleDisplayName ?? 'Admin' }}</div>
      </a>

      <hr class="sidebar-divider my-0">

      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route($routePrefix('dashboard')) }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!-- Components - Only visible to Admin -->
      @if(Auth::user()->role == 'Admin')
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents"
           aria-expanded="true" aria-controls="collapseComponents">
          <i class="fas fa-fw fa-folder"></i>
          <span>Pengurusan Fail</span>
        </a>
        <div id="collapseComponents" class="collapse" aria-labelledby="headingComponents"
             data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Operasi Fail:</h6>
            <a class="collapse-item" href="{{ route($routePrefix('manageFiles')) }}">Senarai Fail</a>
            <a class="collapse-item" href="{{ route($routePrefix('ffaddFile')) }}">Tambah Fail</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">
      @endif

      <!--Manage Request Dropdown -->
      <li class="nav-item active">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRequest"
           aria-expanded="true" aria-controls="collapseRequest">
          <i class="fas fa-tasks"></i>
          <span>Pengurusan Permohonan</span>
        </a>
        <div id="collapseRequest" class="collapse show" aria-labelledby="headingRequest"
             data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Operasi Permohonan:</h6>
            <a class="collapse-item active" href="{{ route($routePrefix('manageRequest')) }}">Permohonan Fail</a>
            <a class="collapse-item" href="{{ route($routePrefix('requestStatus')) }}">Status Tempahan</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Manage Request -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route($routePrefix('manageReturn')) }}">
          <i class="fas fa-file-export"></i>
          <span>Pemulangan Fail</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Track History -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route($routePrefix('requestHistory')) }}">
          <i class="fas fa-history"></i>
          <span>Sejarah Permohonan</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Manage User - Only visible to Admin -->
      @if(Auth::user()->role == 'Admin')
      <li class="nav-item">
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

    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <!-- Sidebar Toggle -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
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

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Peminjaman Fail</h1>
          </div>

          <!-- Content Row -->
          <div class="row">
            <div class="col-12">
              <div class="card shadow-sm" style="background:white; border-radius:8px;">
                <div class="card-body">
                  <div class="table-responsive-sm">
                    <table class="table table-bordered table-hover table-striped" id="requestsTable" width="100%" cellspacing="0">
                      <thead class="thead-light">
                        <tr>
                          <th>Bil.</th>
                          <th>Kod Fail</th>
                          <th>Nama Fail</th>
                          <th>Status Pinjaman</th>
                          <th>Tindakan</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

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
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="successModalLabel">
            <i class="fas fa-check-circle mr-2"></i>Berjaya
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="successMessage"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Error Modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="errorModalLabel">
            <i class="fas fa-exclamation-triangle mr-2"></i>Ralat
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="errorMessage"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
       aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Bersedia untuk Log Keluar?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Pilih "Log Keluar" di bawah jika anda bersedia untuk menamatkan sesi semasa.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <a class="btn btn-primary" href="{{ route('logout') }}"
             onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            Log Keluar
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Core JavaScript-->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function() {
        var table = $('#requestsTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            language: {
                "search": "Cari:",
                "lengthMenu": "Papar _MENU_ rekod setiap halaman",
                "zeroRecords": "Tiada rekod dijumpai",
                "info": "Menunjukkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tiada rekod tersedia",
                "infoFiltered": "(ditapis dari _MAX_ jumlah rekod)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Seterusnya",
                    "previous": "Sebelumnya"
                }
            },
            ajax: {
                url: '{{ route('admin.requests.data') }}',
                error: function(xhr, error, code) {
                    console.log('Ajax error:', xhr.responseText);
                    $('#errorMessage').text('Error loading data: ' + xhr.responseText);
                    $('#errorModal').modal('show');
                }
            },
            columns: [
                { data: null, render: function (data, type, row, meta) { 
                    return meta.row + 1; 
                },searchable: false },
                { data: 'file_code', name: 'file_code' },
                { data: 'file_name', name: 'file_name' },
                { data: 'borrow_status', name: 'borrow_status', orderable: false },
                { 
                    data: 'action', 
                    name: 'action',
                    orderable: false, 
                    searchable: false
                }
            ]
        });
    });

    // Show success modal if session has success message
    @if(session('success'))
        $(document).ready(function() {
            $('#successMessage').text('{{ session('success') }}');
            $('#successModal').modal('show');
        });
    @endif

    // Show error modal if session has error message
    @if(session('error'))
        $(document).ready(function() {
            $('#errorMessage').text('{{ session('error') }}');
            $('#errorModal').modal('show');
        });
    @endif
  </script>
</body>
</html>