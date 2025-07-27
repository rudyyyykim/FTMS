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
    <title>Urus Fail – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Inline Custom Styles -->
    <style>
        #accordionSidebar {
            background-color: #007b78 !important;
            background-image: none !important;
        }

        .sidebar-brand-text {
            color: white;
        }
        
        /* Custom search bar and button layout */
        .dataTables_wrapper .row:first-child {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            margin-bottom: 1rem;
        }
        .dataTables_length {
            flex: 0 0 auto;
            margin-right: 1rem;
        }
        .dataTables_filter {
            flex: 1;
            margin-left: 1rem;
            margin-right: 15px;
        }
        .dataTables_filter input {
            margin-left: 0.5rem;
        }

        .dataTables_filter .btn {
            margin-left: 15px;
        }
        .create-file-btn-container {
            flex: 0 0 auto;
        }
        
        /* Button styling */
        .btn-edit {
            color: #fff;
            background-color: #36b9cc;
            border-color: #36b9cc;
        }
        .btn-delete {
            color: #fff;
            background-color: #e74a3b;
            border-color: #e74a3b;
        }
        
        /* DataTable enhancements */
        .table th {
            background-color: #f8f9fc;
            border-color: #e3e6f0;
            font-weight: 600;
            cursor: pointer;
        }
        
        .table th.sorting,
        .table th.sorting_asc,
        .table th.sorting_desc {
            cursor: pointer;
            position: relative;
        }
        
        .table th.sorting:hover,
        .table th.sorting_asc:hover,
        .table th.sorting_desc:hover {
            background-color: #eaecf4;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        /* Search input styling */
        .dataTables_filter input {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .dataTables_filter input:focus {
            border-color: #007b78;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 120, 0.25);
            outline: 0;
        }
        
        /* Processing indicator */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper .row:first-child {
                flex-wrap: wrap;
            }
            .dataTables_length,
            .dataTables_filter,
            .create-file-btn-container {
                flex: 0 0 100%;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar (unchanged from original) -->
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
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!-- Components -->
      <li class="nav-item active">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseComponents"
           aria-expanded="true" aria-controls="collapseComponents">
          <i class="fas fa-fw fa-folder"></i>
          <span>Pengurusan Fail</span>
        </a>
        <div id="collapseComponents" class="collapse" aria-labelledby="headingComponents"
             data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Operasi Fail:</h6>
            <a class="collapse-item" href="{{ route('admin.manageFiles') }}">Senarai Fail</a>
            <a class="collapse-item" href="{{ route('admin.ffaddFile') }}">Tambah Fail</a>
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

      <!--Manage Return -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.manageReturn') }}">
          <i class="fas fa-file-export"></i>
          <span>Pemulangan Fail</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Track History -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.requestHistory') }}">
          <i class="fas fa-history"></i>
          <span>Sejarah Permohonan</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Manage User -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.manageUser') }}">
          <i class="fas fa-users-cog"></i>
          <span>Urus pengguna</span>
        </a>
      </li>

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

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Senarai Fail</h1>

          <!-- DataTable Container -->
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="filesTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                      <tr>
                          <th>No.</th>
                          <th>Fungsi</th>
                          <th>Kod Fail</th>
                          <th>Nama Fail</th>
                          <th>Keterangan Fail</th>
                          <th>Lokasi Fail</th>
                          <th>Butiran</th>
                          <th>Tindakan</th>
                          <th>Padam</th>
                      </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Description Modal -->
          <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="descriptionModalLabel">Keterangan Fail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="descriptionContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
          </div>

          <!-- Delete Confirmation Modal -->
          <div class="modal fade" id="deleteFileModal" tabindex="-1" role="dialog" aria-labelledby="deleteFileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title" id="deleteFileModalLabel">Padam Fail</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  Adakah anda pasti mahu memadam fail ini? Tindakan ini adalah muktamad.
                  <input type="hidden" id="file_id_to_delete">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-danger" id="confirmFileDelete">Padam</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Success Alert Modal -->
          <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">Berjaya</h5>
                  <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" id="successMessage"></div>
                <div class="modal-footer">
                  <button class="btn btn-success" type="button" data-dismiss="modal">OK</button>
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

  <!-- Core JavaScript-->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function() {
      var table = $('#filesTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.22/i18n/Malay.json',
            search: "Cari:",
            searchPlaceholder: "Cari dalam semua medan...",
            processing: "Memproses...",
            lengthMenu: "Papar _MENU_ rekod per halaman",
            info: "Memapar _START_ hingga _END_ daripada _TOTAL_ rekod",
            infoEmpty: "Memapar 0 hingga 0 daripada 0 rekod",
            infoFiltered: "(ditapis daripada _MAX_ jumlah rekod)",
            emptyTable: "Tiada data dalam jadual",
            zeroRecords: "Tiada rekod yang sepadan dijumpai"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        order: [[1, 'asc']], // Default sort by Function column
        initComplete: function() {
            // Add create button next to search
            $('.dataTables_filter').css('float', 'right');
            $('.dataTables_filter input').attr('placeholder', 'Cari fail...');
            $('.dataTables_filter').append(
                '<span style="margin-left: 15px;"></span>' +
                '<a href="{{ route('admin.ffaddFile') }}" class="btn btn-primary ml-2">' +
                '<i class="fas fa-plus"></i> Tambah Fail</a>'
            );
        },
        ajax: {
          url: '{{ route('admin.files.data') }}',
        },
        columns: [
          { 
            data: null, 
            render: function (data, type, row, meta) { return meta.row + 1; }, 
            orderable: false, 
            searchable: false,
            width: "5%"
          },
          { 
            data: 'function', 
            name: 'function',
            title: 'Fungsi',
            orderable: true,
            searchable: true
          },
          { 
            data: 'file_code', 
            name: 'file_code',
            title: 'Kod Fail',
            orderable: true,
            searchable: true
          },
          { 
            data: 'file_name', 
            name: 'file_name',
            title: 'Nama Fail',
            orderable: true,
            searchable: true
          },
          { 
            data: 'file_description', 
            name: 'file_description',
            title: 'Keterangan',
            orderable: true,
            searchable: true,
            render: function(data, type, row) {
              if (type === 'display') {
                return data && data.length > 50 ? data.substring(0, 50) + '...' : (data || '-');
              }
              return data || '-';
            }
          },
          { 
            data: 'file_location', 
            name: 'file_location',
            title: 'Lokasi',
            orderable: true,
            searchable: true
          },
          { 
            data: 'description_button', 
            name: 'description_button',
            title: 'Butiran',
            orderable: false,
            searchable: false,
            width: "8%"
          },
          { 
            data: 'edit_button', 
            name: 'edit_button',
            title: 'Tindakan',
            orderable: false,
            searchable: false,
            width: "10%"
          },
          { 
            data: 'delete_button', 
            name: 'delete_button',
            title: 'Padam',
            orderable: false,
            searchable: false,
            width: "8%"
          }
        ]
      });

      // Show description in modal
      $('#descriptionModal').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget);
          var description = button.data('description');
          var modal = $(this);
          modal.find('.modal-body').html(description);
      });

      // Handle delete button click
      $(document).on('click', '.delete-file-btn', function() {
        var fileId = $(this).data('id');
        $('#file_id_to_delete').val(fileId);
        $('#deleteFileModal').modal('show');
      });

      // Handle confirm delete button click
      $('#confirmFileDelete').click(function() {
        var fileId = $('#file_id_to_delete').val();
        
        $.ajax({
          url: '{{ url("admin/manage-files") }}/' + fileId,
          type: 'DELETE',
          data: {
            '_token': '{{ csrf_token() }}'
          },
          success: function(result) {
            $('#deleteFileModal').modal('hide');
            $('#successMessage').html('Fail berjaya dipadam.');
            $('#successModal').modal('show');
            $('#filesTable').DataTable().ajax.reload();
          },
          error: function(xhr) {
            $('#deleteFileModal').modal('hide');
            alert('Ralat berlaku semasa memadam fail. Sila cuba lagi.');
          }
        });
      });
    });
  </script>
</body>
</html>