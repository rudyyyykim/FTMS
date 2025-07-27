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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sejarah Permohonan – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css" rel="stylesheet">
    
    <!-- Inline Custom Styles -->
    <style>
        /* Custom sidebar background color and no gradient */
        #accordionSidebar {
            background-color: #007b78 !important;
            background-image: none !important;
        }

        /* Customize the sidebar brand text and icon */
        .sidebar-brand-text {
            color: white;
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
        
        /* Custom styles for history page */
        .badge-approved {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        .badge-returned {
            background-color: #17a2b8;
            color: white;
        }
        
        /* Filter panel styles */
        .filter-panel {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filter-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #007b78;
        }
        
        /* Horizontal scroll improvements */
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        /* Ensure table maintains minimum width for all columns */
        #historyTable th,
        #historyTable td {
            white-space: nowrap;
            vertical-align: middle;
        }
        
        /* Button styling for small screens */
        .dt-buttons .btn {
            margin-bottom: 5px;
            margin-right: 5px;
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
        <a class="nav-link" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.dashboard' : 'admin.dashboard') }}">
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
            <a class="collapse-item" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.manageFiles' : 'admin.manageFiles') }}">Senarai Fail</a>
            <a class="collapse-item" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.ffaddFile' : 'admin.ffaddFile') }}">Tambah Fail</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">
      @endif

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
            <a class="collapse-item" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.manageRequest' : 'admin.manageRequest') }}">Permohonan Fail</a>
            <a class="collapse-item" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.requestStatus' : 'admin.requestStatus') }}">Status Tempahan</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Manage Return -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.manageReturn' : 'admin.manageReturn') }}">
          <i class="fas fa-file-export"></i>
          <span>Pemulangan Fail</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Track History -->
      <li class="nav-item active">
        <a class="nav-link" href="{{ route(Auth::user()->role == 'Pka' ? 'pka.requestHistory' : 'admin.requestHistory') }}">
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
                <a class="dropdown-item" href="{{ route($routePrefix('manageProfile')) }}">
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
            <h1 class="h3 mb-0 text-gray-800">
              Sejarah Permohonan
              @if(Auth::user()->role == 'Pka')
              @endif
            </h1>
          </div>

          <!-- Filter Panel -->
          <div class="row">
            <div class="col-12">
              <div class="filter-panel">
                <div class="filter-title">Penapis Tarikh Permohonan</div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="filterTiming">Masa Pemulangan</label>
                      <select class="form-control" id="filterTiming">
                        <option value="">Semua</option>
                        <option value="Awal">Awal</option>
                        <option value="Tepat">Tepat Masa</option>
                        <option value="Lewat">Lewat</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="filterDateFrom">Dari Tarikh Permohonan</label>
                      <input type="date" class="form-control" id="filterDateFrom">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="filterDateTo">Hingga Tarikh Permohonan</label>
                      <input type="date" class="form-control" id="filterDateTo">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <div>
                        <button class="btn btn-secondary" id="resetFilters">
                          <i class="fas fa-sync-alt"></i> Set Semula
                        </button>
                        <button class="btn btn-sidebar" id="applyFilters">
                          <i class="fas fa-filter"></i> Tapis
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Row -->
          <div class="row">
            <div class="col-12">
              <div class="card shadow-sm" style="background:white; border-radius:8px;">
                <div class="card-body">
                  <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-bordered table-hover table-striped" id="historyTable" style="min-width: 1200px;" cellspacing="0">
                      <thead class="thead-light">
                        <tr>
                          <th style="min-width: 50px;">Bil.</th>
                          <th style="min-width: 100px;">ID Permohonan</th>
                          <th style="min-width: 120px;">Tarikh Mohon</th>
                          <th style="min-width: 100px;">ID Pemulangan</th>
                          <th style="min-width: 120px;">Tarikh Pulang</th>
                          <th style="min-width: 120px;">Maklumat Fail</th>
                          <th style="min-width: 120px;">Maklumat Staff</th>
                          <th style="min-width: 100px;">Masa Pulang</th>
                          <th style="min-width: 120px;">Dikendalikan Oleh</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Data will be populated by DataTables via AJAX -->
                      </tbody>
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

  <!-- File Details Modal -->
  <div class="modal fade" id="fileDetailsModal" tabindex="-1" role="dialog" aria-labelledby="fileDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fileDetailsModalLabel">Maklumat Fail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Kod Fail:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Nama Fail:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Kategori:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Lokasi Fail:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Status:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Tarikh Dicipta:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Dicipta Oleh:</div>
            <div class="col-md-8"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sidebar" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Staff Details Modal -->
  <div class="modal fade" id="staffDetailsModal" tabindex="-1" role="dialog" aria-labelledby="staffDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staffDetailsModalLabel">Maklumat Staff</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Nama:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Jawatan:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">No. Telefon:</div>
            <div class="col-md-8"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Emel:</div>
            <div class="col-md-8"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sidebar" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Report Options Modal -->
  <div class="modal fade" id="reportOptionsModal" tabindex="-1" role="dialog" aria-labelledby="reportOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reportOptionsModalLabel">Pilihan Laporan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="reportFormat">Format Laporan</label>
            <select class="form-control" id="reportFormat">
              <option value="pdf">PDF</option>
              <option value="excel">Excel</option>
              <option value="csv">CSV</option>
              <option value="print">Cetak</option>
            </select>
          </div>
          <div class="form-group">
            <label for="reportRange">Julat Tarikh</label>
            <select class="form-control" id="reportRange">
              <option value="all">Semua Rekod</option>
              <option value="today">Hari Ini</option>
              <option value="week">Minggu Ini</option>
              <option value="month">Bulan Ini</option>
              <option value="custom">Pilih Tarikh</option>
            </select>
          </div>
          <div class="row" id="customDateRange" style="display:none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="reportDateFrom">Dari</label>
                <input type="date" class="form-control" id="reportDateFrom">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="reportDateTo">Hingga</label>
                <input type="date" class="form-control" id="reportDateTo">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-sidebar" id="generateReportBtn">Jana Laporan</button>
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
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.colVis.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function() {
        // Initialize DataTable with AJAX data source
        var table = $('#historyTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            destroy: true, // Allow reinitializing
            ajax: {
                url: '{{ route($routePrefix("requestHistory.data")) }}',
                data: function (d) {
                    d.timing = $('#filterTiming').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'request_id', name: 'request_id', searchable: true },
                { data: 'request_date', name: 'request_date', searchable: false },
                { data: 'return_id', name: 'return_id', searchable: true },
                { data: 'actual_return_date', name: 'actual_return_date', searchable: false },
                { 
                    data: 'file_info', 
                    name: 'file_code',
                    searchable: true,
                    render: function(data, type, row) {
                        if (data && typeof data === 'object') {
                            return '<button class="btn btn-sm btn-info view-file-details" ' +
                                   'data-file-code="' + data.file_code + '" ' +
                                   'data-file-name="' + data.file_name + '" ' +
                                   'data-file-location="' + data.file_location + '" ' +
                                   'data-file-description="' + data.file_description + '" ' +
                                   'data-file-level="' + data.file_level + '" ' +
                                   'data-created-by="' + data.created_by + '">' +
                                   '<i class="fas fa-file-alt"></i> Lihat</button>';
                        }
                        return '-';
                    }
                },
                { 
                    data: 'staff_info', 
                    name: 'staff_name',
                    searchable: true,
                    render: function(data, type, row) {
                        if (data && typeof data === 'object') {
                            return '<button class="btn btn-sm btn-info view-staff-details" ' +
                                   'data-staff-name="' + data.staff_name + '" ' +
                                   'data-staff-phone="' + data.staff_phone + '" ' +
                                   'data-staff-email="' + data.staff_email + '" ' +
                                   'data-staff-position="' + data.staff_position + '">' +
                                   '<i class="fas fa-user"></i> Lihat</button>';
                        }
                        return '-';
                    }
                },
                { data: 'return_timing', name: 'return_timing', orderable: false, searchable: false },
                { data: 'handled_by', name: 'handled_by', searchable: true }
            ],
            @if(Auth::user()->role == 'Admin')
            dom: 'B<"row"<"col-sm-6"l><"col-sm-6"f>>rtip', // B=buttons, length and filter on same row, r=processing, t=table, i=info, p=pagination
            @else
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip', // length and filter on same row for Pka users
            @endif
            @if(Auth::user()->role == 'Admin')
            buttons: [
                {
                    text: '<i class="fas fa-copy"></i> Salin Terperinci',
                    className: 'btn btn-secondary btn-sm',
                    action: function() {
                        exportDetailedData('copy');
                    }
                },
                {
                    text: '<i class="fas fa-file-csv"></i> CSV Terperinci',
                    className: 'btn btn-success btn-sm',
                    action: function() {
                        exportDetailedData('csv');
                    }
                },
                {
                    text: '<i class="fas fa-file-pdf"></i> PDF Terperinci',
                    className: 'btn btn-danger btn-sm',
                    action: function() {
                        exportDetailedData('pdf');
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Cetak',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 7, 8] // Standard table columns only
                    },
                    title: 'Sejarah Permohonan - ' + new Date().toLocaleDateString('ms-MY')
                }
            ],
            @else
            buttons: [], // No export buttons for Pka users
            @endif
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            pageLength: 25, // Default page length
            language: {
                "search": "Cari:",
                "lengthMenu": "Papar _MENU_ rekod setiap halaman",
                "zeroRecords": "Tiada rekod sejarah dijumpai",
                "info": "Menunjukkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tiada rekod tersedia",
                "infoFiltered": "(ditapis dari _MAX_ jumlah rekod)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Seterusnya",
                    "previous": "Sebelumnya"
                },
                "processing": "Memproses..."
            }
        });

        @if(Auth::user()->role == 'Admin')
        // Function to export detailed data (Admin only)
        function exportDetailedData(format) {
            // Get current filter values
            var filterData = {
                format: format,
                range: 'all',
                timing: $('#filterTiming').val(),
                date_from: $('#filterDateFrom').val(),
                date_to: $('#filterDateTo').val(),
                search: table.search() // Include current search term
            };
            
            // Make AJAX call to export detailed data
            $.ajax({
                url: '{{ route($routePrefix("requestHistory.export")) }}',
                method: 'POST',
                data: filterData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (format === 'copy') {
                            copyDetailedDataToClipboard(response.data);
                        } else if (format === 'csv') {
                            downloadDetailedExport(response.data, 'csv');
                        } else if (format === 'pdf') {
                            generateDetailedPDF(response.data);
                        }
                    }
                },
                error: function() {
                    alert('Ralat semasa menjana laporan terperinci. Sila cuba lagi.');
                }
            });
        }
        @endif

        // Handle file details modal
        $(document).on('click', '.view-file-details', function() {
            var button = $(this);
            
            $('#fileDetailsModal .modal-body .row').eq(0).find('.col-md-8').text(button.data('file-code'));
            $('#fileDetailsModal .modal-body .row').eq(1).find('.col-md-8').text(button.data('file-name'));
            $('#fileDetailsModal .modal-body .row').eq(2).find('.col-md-8').text('Pentadbiran'); // You might want to add category to data
            $('#fileDetailsModal .modal-body .row').eq(3).find('.col-md-8').text(button.data('file-location'));
            $('#fileDetailsModal .modal-body .row').eq(4).find('.col-md-8').html('<span class="badge badge-success">Dipulangkan</span>');
            $('#fileDetailsModal .modal-body .row').eq(5).find('.col-md-8').text(new Date().toLocaleDateString('ms-MY'));
            $('#fileDetailsModal .modal-body .row').eq(6).find('.col-md-8').text(button.data('created-by'));
            
            $('#fileDetailsModal').modal('show');
        });

        // Handle staff details modal
        $(document).on('click', '.view-staff-details', function() {
            var button = $(this);
            
            $('#staffDetailsModal .modal-body .row').eq(0).find('.col-md-8').text(button.data('staff-name'));
            $('#staffDetailsModal .modal-body .row').eq(1).find('.col-md-8').text(button.data('staff-position'));
            $('#staffDetailsModal .modal-body .row').eq(2).find('.col-md-8').text(button.data('staff-phone'));
            $('#staffDetailsModal .modal-body .row').eq(3).find('.col-md-8').text(button.data('staff-email'));
            
            $('#staffDetailsModal').modal('show');
        });

        // Show custom date range when selected
        $('#reportRange').change(function() {
            if ($(this).val() === 'custom') {
                $('#customDateRange').show();
            } else {
                $('#customDateRange').hide();
            }
        });

        @if(Auth::user()->role == 'Admin')
        // Generate report button click (Admin only)
        $('#generateReport').click(function() {
            $('#reportOptionsModal').modal('show');
        });

        // Generate report based on options (Admin only)
        $('#generateReportBtn').click(function() {
            var format = $('#reportFormat').val();
            var range = $('#reportRange').val();
            var dateFrom = $('#reportDateFrom').val();
            var dateTo = $('#reportDateTo').val();
            
            // Get current filter values
            var filterData = {
                format: format,
                range: range,
                custom_date_from: dateFrom,
                custom_date_to: dateTo,
                timing: $('#filterTiming').val(),
                date_from: $('#filterDateFrom').val(),
                date_to: $('#filterDateTo').val(),
                search: table.search() // Include current search term
            };
            
            // Make AJAX call to export data with detailed information
            $.ajax({
                url: '{{ route($routePrefix("requestHistory.export")) }}',
                method: 'POST',
                data: filterData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Use detailed export functions
                        if (format === 'excel' || format === 'csv') {
                            downloadDetailedExport(response.data, 'csv');
                        } else if (format === 'pdf') {
                            generateDetailedPDF(response.data);
                        } else if (format === 'print') {
                            // For print, we'll show the data in a new window
                            printDetailedData(response.data);
                        }
                    }
                },
                error: function() {
                    alert('Ralat semasa menjana laporan. Sila cuba lagi.');
                }
            });
            
            $('#reportOptionsModal').modal('hide');
        });
        @endif

        @if(Auth::user()->role == 'Admin')
        // Export utility functions (Admin only)
        
        // Function to download detailed export as CSV
        function downloadDetailedExport(data, format) {
            if (data.length === 0) {
                alert('Tiada data untuk diekport.');
                return;
            }

            var csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // Add BOM for UTF-8
            
            // Add headers with detailed information
            var headers = Object.keys(data[0]);
            csvContent += headers.join(",") + "\n";
            
            // Add data rows
            data.forEach(function(row) {
                var values = headers.map(function(header) {
                    var value = row[header] || '';
                    // Escape commas and quotes for CSV
                    if (typeof value === 'string' && (value.includes(',') || value.includes('"') || value.includes('\n'))) {
                        value = '"' + value.replace(/"/g, '""') + '"';
                    }
                    return value;
                });
                csvContent += values.join(",") + "\n";
            });

            // Create download link
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "Sejarah_Permohonan_Terperinci_" + new Date().toISOString().slice(0,10) + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            alert('Fail CSV terperinci berjaya dimuat turun!\n\nLaporan mengandungi maklumat lengkap termasuk butiran fail dan staff.');
        }

        // Function to copy detailed data to clipboard
        function copyDetailedDataToClipboard(data) {
            if (data.length === 0) {
                alert('Tiada data untuk disalin.');
                return;
            }

            var textContent = "SEJARAH PERMOHONAN FAIL - LAPORAN TERPERINCI\n";
            textContent += "Tarikh Jana: " + new Date().toLocaleDateString('ms-MY') + "\n\n";
            
            // Add headers
            var headers = Object.keys(data[0]);
            textContent += headers.join("\t") + "\n";
            textContent += headers.map(() => "=".repeat(15)).join("\t") + "\n";
            
            // Add data rows
            data.forEach(function(row) {
                var values = headers.map(function(header) {
                    var value = row[header] || '';
                    // Replace tabs and newlines for proper formatting
                    return String(value).replace(/[\t\n\r]/g, ' ');
                });
                textContent += values.join("\t") + "\n";
            });

            // Copy to clipboard
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textContent).then(function() {
                    alert('Data terperinci berjaya disalin ke papan keratan!\n\nAnda boleh menampal data ini ke dalam aplikasi lain (Excel, Word, dll).');
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    fallbackCopyTextToClipboard(textContent);
                });
            } else {
                fallbackCopyTextToClipboard(textContent);
            }
        }

        // Fallback method for copying to clipboard
        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    alert('Data terperinci berjaya disalin ke papan keratan!\n\nAnda boleh menampal data ini ke dalam aplikasi lain (Excel, Word, dll).');
                } else {
                    alert('Tidak dapat menyalin data. Sila cuba lagi atau gunakan pilihan muat turun.');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                alert('Tidak dapat menyalin data. Sila cuba lagi atau gunakan pilihan muat turun.');
            }
            document.body.removeChild(textArea);
        }

        // Function to generate detailed PDF
        function generateDetailedPDF(data) {
            if (data.length === 0) {
                alert('Tiada data untuk dijana dalam PDF.');
                return;
            }

            // Create PDF content with A3 size and smaller margins for more space
            var docDefinition = {
                pageSize: 'A3',
                pageOrientation: 'landscape',
                pageMargins: [10, 15, 10, 15],
                content: [
                    {
                        text: 'SEJARAH PERMOHONAN FAIL - LAPORAN TERPERINCI',
                        style: 'header',
                        alignment: 'center',
                        margin: [0, 0, 0, 15]
                    },
                    {
                        text: 'Tarikh Jana: ' + new Date().toLocaleDateString('ms-MY'),
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 15]
                    }
                ],
                styles: {
                    header: {
                        fontSize: 14,
                        bold: true
                    },
                    subheader: {
                        fontSize: 10,
                        bold: true
                    },
                    tableHeader: {
                        fontSize: 7,
                        bold: true,
                        fillColor: '#eeeeee'
                    },
                    tableCell: {
                        fontSize: 6
                    }
                },
                defaultStyle: {
                    font: 'Roboto'
                }
            };

            if (data.length > 0) {
                var headers = Object.keys(data[0]);
                
                // Define custom column widths based on content type
                var columnWidths = headers.map(function(header) {
                    switch(header.toLowerCase()) {
                        case 'bil':
                        case 'no':
                            return 25;
                        case 'id_permohonan':
                        case 'id_pemulangan':
                            return 40;
                        case 'tarikh_mohon':
                        case 'tarikh_dijangka_pulang':
                        case 'tarikh_sebenar_pulang':
                            return 50;
                        case 'kod_fail':
                            return 45;
                        case 'nama_fail':
                            return 80;
                        case 'lokasi_fail':
                            return 60;
                        case 'penerangan_fail':
                        case 'tahap_fail':
                            return 70;
                        case 'nama_staff':
                        case 'dikendalikan_oleh':
                            return 55;
                        case 'jawatan_staff':
                            return 60;
                        case 'telefon_staff':
                        case 'emel_staff':
                            return 55;
                        case 'masa_pulang':
                            return 35;
                        default:
                            return 'auto';
                    }
                });

                // Prepare table headers with word wrap
                var tableHeaders = headers.map(header => ({
                    text: header.replace(/_/g, ' ').toUpperCase(),
                    style: 'tableHeader',
                    alignment: 'center'
                }));

                // Prepare table body with word wrap
                var tableBody = [tableHeaders];
                
                data.forEach(function(row) {
                    var rowData = headers.map(function(header) {
                        var value = row[header] || '';
                        var text = String(value);
                        
                        // Truncate very long text to prevent overflow
                        if (text.length > 50) {
                            text = text.substring(0, 47) + '...';
                        }
                        
                        return {
                            text: text,
                            style: 'tableCell',
                            alignment: ['bil', 'no', 'id_permohonan', 'id_pemulangan'].includes(header.toLowerCase()) ? 'center' : 'left'
                        };
                    });
                    tableBody.push(rowData);
                });

                // Add table to document with custom layout
                docDefinition.content.push({
                    table: {
                        headerRows: 1,
                        widths: columnWidths,
                        body: tableBody,
                        dontBreakRows: false,
                        keepWithHeaderRows: 1
                    },
                    layout: {
                        fillColor: function (rowIndex, node, columnIndex) {
                            return (rowIndex === 0) ? '#eeeeee' : (rowIndex % 2 === 0 ? '#f9f9f9' : null);
                        },
                        hLineWidth: function (i, node) {
                            return 0.5;
                        },
                        vLineWidth: function (i, node) {
                            return 0.5;
                        },
                        hLineColor: function (i, node) {
                            return '#cccccc';
                        },
                        vLineColor: function (i, node) {
                            return '#cccccc';
                        },
                        paddingLeft: function(i, node) { return 2; },
                        paddingRight: function(i, node) { return 2; },
                        paddingTop: function(i, node) { return 1; },
                        paddingBottom: function(i, node) { return 1; }
                    }
                });
            }

            // Generate and download PDF
            try {
                pdfMake.createPdf(docDefinition).download('Sejarah_Permohonan_Terperinci_' + new Date().toISOString().slice(0,10) + '.pdf');
                alert('PDF terperinci berjaya dijana dan dimuat turun!\n\nLaporan mengandungi maklumat lengkap termasuk butiran fail dan staff.\n\nNota: PDF dijana dalam saiz A3 untuk paparan terbaik bagi semua kolum.');
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Ralat semasa menjana PDF. Sila cuba lagi atau gunakan pilihan export lain.');
            }
        }

        // Function to print detailed data
        function printDetailedData(data) {
            if (data.length === 0) {
                alert('Tiada data untuk dicetak.');
                return;
            }

            var printWindow = window.open('', '_blank');
            var printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Sejarah Permohonan - Laporan Terperinci</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .header h1 { margin: 0; font-size: 18px; }
                        .header p { margin: 5px 0; font-size: 12px; }
                        table { width: 100%; border-collapse: collapse; font-size: 10px; }
                        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
                        th { background-color: #f5f5f5; font-weight: bold; }
                        tr:nth-child(even) { background-color: #f9f9f9; }
                        @media print {
                            body { margin: 10px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>SEJARAH PERMOHONAN FAIL - LAPORAN TERPERINCI</h1>
                        <p>Tarikh Jana: ${new Date().toLocaleDateString('ms-MY')}</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
            `;

            // Add table headers
            if (data.length > 0) {
                var headers = Object.keys(data[0]);
                headers.forEach(function(header) {
                    printContent += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
                });
                printContent += `
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Add table rows
                data.forEach(function(row) {
                    printContent += '<tr>';
                    headers.forEach(function(header) {
                        var value = row[header] || '';
                        printContent += `<td>${String(value)}</td>`;
                    });
                    printContent += '</tr>';
                });
            }

            printContent += `
                        </tbody>
                    </table>
                    <div class="no-print" style="text-align: center; margin-top: 20px;">
                        <button onclick="window.print()">Cetak</button>
                        <button onclick="window.close()">Tutup</button>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Auto print after a short delay
            setTimeout(function() {
                printWindow.print();
            }, 500);
        }
        @endif

        // Apply filters
        $('#applyFilters').click(function() {
            table.ajax.reload(null, false); // Keep current page position
        });

        // Reset filters
        $('#resetFilters').click(function() {
            $('#filterTiming').val('');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            table.search('').draw(); // Clear search as well
            table.ajax.reload(null, false); // Keep current page position
        });
    });
  </script>
</body>
</html>