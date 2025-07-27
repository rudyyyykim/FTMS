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
    <title>Pemulangan Fail – {{ config('app.name') }}</title>

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
        
        /* Custom styles for return page */
        .return-date-edit {
            cursor: pointer;
            color: #007b78;
        }
        .return-date-edit:hover {
            text-decoration: underline;
        }
        .badge-overdue {
            background-color: #dc3545;
            color: white;
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        /* Status badges */
        .badge-returned {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }

        /* Timing badges */
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        /* Date display */
        .return-date {
            display: inline-block;
            min-width: 80px;
        }
        /* Style for the actual return date column */
        #returnsTable td:nth-child(8) { /* 8th column */
            text-align: center;
            font-size: 0.9em;
        }

        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        #returnsTable td {
            white-space: normal !important;
            word-wrap: break-word;
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
            <a class="collapse-item" href="{{ route($routePrefix('manageRequest')) }}">Permohonan Fail</a>
            <a class="collapse-item" href="{{ route($routePrefix('requestStatus')) }}">Status Tempahan</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">

      <!--Manage Return -->
      <li class="nav-item active">
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
            <h1 class="h3 mb-0 text-gray-800">Pemulangan Fail</h1>
          </div>

          <!-- Content Row -->
          <div class="row">
            <div class="col-12">
              <div class="card shadow-sm" style="background:white; border-radius:8px;">
                <div class="card-body">
                  <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-bordered table-hover table-striped" id="returnsTable" width="100%" cellspacing="0">
                      <thead class="thead-light">
                        <tr>
                          <th>Bil.</th>
                          <th>ID Pemulangan</th>
                          <th>ID Permohonan</th>
                          <th>Fail</th>
                          <th>Tarikh Pinjam/Tempahan</th>
                          <th>Tarikh Pulang</th>
                          <th>Status</th>
                          <th data-toggle="tooltip" title="Tarikh sebenar fail dipulangkan (untuk pulangan awal/lewat)">
                              Tarikh Sebenar
                          </th>
                          <th>Pegawai</th>
                          <th>Tindakan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Data will be loaded via AJAX -->
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

  <!-- Edit Return Date Modal -->
  <div class="modal fade" id="editReturnDateModal" tabindex="-1" role="dialog" aria-labelledby="editReturnDateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editReturnDateModalLabel">Kemaskini Tarikh Pulang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="newReturnDate">Tarikh Pulang Baru</label>
              <input type="date" class="form-control" id="newReturnDate" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-sidebar" id="saveReturnDate">Simpan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Borrower Details Modal -->
  <div class="modal fade" id="borrowerDetailsModal" tabindex="-1" role="dialog" aria-labelledby="borrowerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="borrowerDetailsModalLabel">Maklumat Peminjam</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Nama:</div>
            <div class="col-md-8" id="borrowerName"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Jawatan:</div>
            <div class="col-md-8" id="borrowerPosition"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">No. Telefon:</div>
            <div class="col-md-8" id="borrowerPhone"></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Emel:</div>
            <div class="col-md-8" id="borrowerEmail"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sidebar" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Return Confirmation Modal -->
  <div class="modal fade" id="returnConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="returnConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="returnConfirmationModalLabel">Sahkan Pemulangan Fail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Adakah anda pasti ingin mengesahkan pemulangan fail ini?</p>
          <p><strong>ID Permohonan:</strong> <span id="confirmRequestId"></span></p>
          <p><strong>Fail:</strong> <span id="confirmFileName"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-sidebar" id="confirmReturn">Ya, Sahkan</button>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#returnsTable').DataTable({
            processing: true,
            serverSide: false,
            autoWidth: true, // Allows columns to shrink/expand
            scrollX: false, // Disable horizontal scroll
            scrollCollapse: true, // Prevent table from expanding beyond container
            paging: true, // Keep pagination
            fixedHeader: true, // Optional: Keeps header fixed while scrolling
            ajax: {
                url: "{{ route('admin.manageReturn.data') }}",
                type: "GET"
            },
            columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
              {data: 'returnID', name: 'returnID'},
              {data: 'requestID', name: 'requestID'},
              {data: 'file_info', name: 'file_info'},
              {data: 'request_date', name: 'request_date'},
              {data: 'return_date_display', name: 'return_date_display'},
              {data: 'return_status', name: 'return_status'},
              {data: 'updated_return_date', name: 'updated_return_date'},
              {data: 'staff_details', name: 'staff_details', orderable: false},
              {data: 'action', name: 'action', orderable: false}
          ],
            responsive: true,
            language: {
                "search": "Cari:",
                "lengthMenu": "Papar _MENU_ rekod setiap halaman",
                "zeroRecords": "Tiada sebarang urusan permohonan atau pemulangan fail buat masa ini",
                "info": "Menunjukkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tiada rekod tersedia",
                "infoFiltered": "(ditapis dari _MAX_ jumlah rekod)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Seterusnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Edit return date functionality
        $(document).on('click', '.return-date-edit', function() {
            var returnId = $(this).data('id');
            var currentDate = $(this).data('date');
            
            // Get the current row
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var rowData = row.data();
            
            // Get request date from the row data
            var requestDate = rowData.request_date;
            if (requestDate && requestDate.includes(' - ')) {
                // If request_date has a range format (e.g., "2025-07-20 - 2025-07-25"), use the first date
                requestDate = requestDate.split(' - ')[0];
            }
            
            // Format date properly if it's not in YYYY-MM-DD format
            if (requestDate && requestDate.includes('/')) {
                var parts = requestDate.split('/');
                requestDate = parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            
            // Get today's date in YYYY-MM-DD format
            var today = new Date();
            var todayFormatted = today.toISOString().split('T')[0];
            
            // Set the minimum date to either the request date or today, whichever is later
            var minDate = todayFormatted; // Default to today
            if (requestDate && new Date(requestDate) > new Date(todayFormatted)) {
                minDate = requestDate;
            }
            
            $('#editReturnDateModal').data('id', returnId);
            $('#newReturnDate').val(currentDate);
            $('#newReturnDate').attr('min', minDate);
            $('#editReturnDateModal').modal('show');
        });

        $('#saveReturnDate').click(function() {
            var returnId = $('#editReturnDateModal').data('id');
            var newDate = $('#newReturnDate').val();
            
            $.ajax({
                url: "{{ url('admin/manage-return') }}/" + returnId + "/update-date",
                type: "PUT",
                data: {
                    returnDate: newDate,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#editReturnDateModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.success);
                },
                error: function(xhr) {
                    showAlert('error', xhr.responseJSON.error || 'Ralat berlaku');
                }
            });
        });

        // View staff details
        $(document).on('click', '.view-staff-btn', function() {
            var returnId = $(this).data('id');
            
            $.get("{{ url('admin/manage-return') }}/" + returnId + "/staff-details", function(data) {
                $('#borrowerName').text(data.name);
                $('#borrowerPhone').text(data.phone);
                $('#borrowerEmail').text(data.email);
                $('#borrowerPosition').text(data.position);
                
                $('#borrowerDetailsModal').modal('show');
            });
        });

        // Return button functionality
        $(document).on('click', '.return-btn', function() {
          var returnId = $(this).data('id');
          
          // Get return details for confirmation modal
          $.ajax({
              url: `/admin/manage-return/${returnId}`,  // Direct URL construction
              type: "GET",
              success: function(data) {
                  $('#confirmRequestId').text(data.requestID);
                  $('#confirmFileName').text(data.file_info);
                  $('#returnConfirmationModal').data('id', returnId);
                  $('#returnConfirmationModal').modal('show');
              },
              error: function(xhr) {
                  showAlert('error', 'Gagal memuat maklumat permohonan');
                  console.error(xhr.responseText);
              }
          });
          // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top'
    });
      });

        $('#confirmReturn').click(function() {
          var returnId = $('#returnConfirmationModal').data('id');
          
          $.ajax({
              url: `/admin/manage-return/${returnId}/process-return`,  // Direct URL construction
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}"
              },
              success: function(response) {
                  $('#returnConfirmationModal').modal('hide');
                  table.ajax.reload(null, false);
                  showAlert('success', response.success);
              },
              error: function(xhr) {
                  showAlert('error', xhr.responseJSON.error || 'Ralat berlaku semasa memproses pemulangan');
                  console.error(xhr.responseText);
              }
          });
      });

        // Helper functions
        function showAlert(type, message) {
            var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                           message +
                           '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                           '<span aria-hidden="true">&times;</span></button></div>';
            
            $('.container-fluid').prepend(alertHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
    });
  </script>
</body>
</html>