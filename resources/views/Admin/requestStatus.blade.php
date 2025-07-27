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
    <title>Status Tempahan Fail – {{ config('app.name') }}</title>

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
    </style>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    {{-- Sidebar --}}
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
            <a class="collapse-item" href="{{ route($routePrefix('manageRequest')) }}">Permohonan Fail</a>
            <a class="collapse-item active" href="{{ route($routePrefix('requestStatus')) }}">Status Tempahan</a>
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
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
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
         
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Status Tempahan Fail</h1>
          </div>

          <!-- Success Message -->
          @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif

          <!-- Content Row -->
          <div class="row">
            <div class="col-12">
              <div class="card shadow-sm" style="background:white; border-radius:8px;">
                <div class="card-body">
                  <div class="table-responsive-sm">
                    <table class="table table-bordered table-hover table-striped" id="reservationTable" width="100%" cellspacing="0">
                      <thead class="thead-light">
                        <tr>
                          <th>Bil.</th>
                          <th>ID Permohonan</th>
                          <th>Nama Peminjam</th>
                          <th>Kod Fail</th>
                          <th>Tarikh Permohonan</th>
                          <th>Tarikh Tempahan</th>
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

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="confirmModalLabel">
            <i class="fas fa-question-circle mr-2"></i>Pengesahan
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="confirmMessage"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-warning" id="confirmAction">Ya, Teruskan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Proceed Reservation Modal -->
  <div class="modal fade" id="proceedModal" tabindex="-1" role="dialog" aria-labelledby="proceedModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="proceedModalLabel">
            <i class="fas fa-calendar-check mr-2"></i>Teruskan Tempahan
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Sila tetapkan tarikh jangka pulang untuk tempahan ini:</p>
          <div class="form-group">
            <label for="returnDate">Tarikh Jangka Pulang <span class="text-danger">*</span></label>
            <input type="date" id="returnDate" class="form-control" required>
            <small class="form-text text-muted">
              <i class="fas fa-info-circle mr-1"></i>
              Tarikh mestilah selepas tarikh tempahan: <span id="minReturnDate"></span>
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success" id="proceedAction">Teruskan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Scripts -->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function() {
        var table = $('#reservationTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            order: [[1, 'desc']], // Order by request_id column descending
            language: {
                "search": "Cari:",
                "lengthMenu": "Papar _MENU_ rekod setiap halaman",
                "zeroRecords": "Tiada tempahan atau jadual fail buat masa ini",
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
                url: '{{ route('admin.requestStatus.data') }}',
                error: function(xhr, error, code) {
                    console.log('Ajax error:', xhr.responseText);
                    $('#errorMessage').text('Error loading data: ' + xhr.responseText);
                    $('#errorModal').modal('show');
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                { data: 'request_id', name: 'request_id' },
                { data: 'borrower_name', name: 'borrower_name' },
                { data: 'file_code', name: 'file_code' },
                { data: 'request_date', name: 'request_date' },
                { data: 'reserve_date', name: 'reserve_date' },
                { 
                    data: 'action', 
                    name: 'action',
                    orderable: false, 
                    searchable: false
                }
            ]
        });
    });

    // Cancel reservation function
    function cancelReservation(requestID) {
        $('#confirmMessage').text('Adakah anda pasti ingin membatalkan tempahan ini?');
        $('#confirmModal').modal('show');
        
        $('#confirmAction').off('click').on('click', function() {
            $('#confirmModal').modal('hide');
            
            $.ajax({
                url: 'request-status/' + requestID + '/cancel',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
                        $('#reservationTable').DataTable().ajax.reload();
                    } else {
                        $('#errorMessage').text('Gagal membatalkan tempahan: ' + response.message);
                        $('#errorModal').modal('show');
                    }
                },
                error: function(xhr) {
                    $('#errorMessage').text('Ralat ketika membatalkan tempahan');
                    $('#errorModal').modal('show');
                }
            });
        });
    }

    // Proceed reservation function
    function proceedReservation(requestID) {
        // Get reservation data first to set proper minimum date
        $.ajax({
            url: 'request-status/' + requestID + '/info',
            method: 'GET',
            success: function(reservationInfo) {
                // Show the proceed modal with return date input
                $('#proceedModal').modal('show');
                
                // Set minimum date to the day after reservation date
                const reserveDate = new Date(reservationInfo.reserve_date);
                reserveDate.setDate(reserveDate.getDate() + 1);
                const minDate = reserveDate.toISOString().split('T')[0];
                $('#returnDate').attr('min', minDate);
                $('#minReturnDate').text(reserveDate.toLocaleDateString('ms-MY'));
                
                // Clear previous value
                $('#returnDate').val('');
                
                $('#proceedAction').off('click').on('click', function() {
                    const returnDate = $('#returnDate').val();
                    
                    if (!returnDate) {
                        alert('Sila pilih tarikh jangka pulang');
                        return;
                    }
                    
                    if (returnDate < minDate) {
                        alert('Tarikh jangka pulang mestilah selepas tarikh tempahan');
                        return;
                    }
                    
                    $('#proceedModal').modal('hide');
                    
                    $.ajax({
                        url: 'request-status/' + requestID + '/proceed',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            return_date: returnDate
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#successMessage').text(response.message);
                                $('#successModal').modal('show');
                                $('#reservationTable').DataTable().ajax.reload();
                            } else {
                                $('#errorMessage').text('Gagal memproses tempahan: ' + response.message);
                                $('#errorModal').modal('show');
                            }
                        },
                        error: function(xhr) {
                            console.log('Error details:', xhr.responseText);
                            console.log('Status:', xhr.status);
                            
                            let errorMessage = 'Ralat ketika memproses tempahan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = response.message || errorMessage;
                                } catch (e) {
                                    errorMessage = xhr.responseText;
                                }
                            }
                            
                            $('#errorMessage').text(errorMessage);
                            $('#errorModal').modal('show');
                        }
                    });
                });
            },
            error: function(xhr) {
                // Fallback to tomorrow if can't get reservation info
                $('#proceedModal').modal('show');
                
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const minDate = tomorrow.toISOString().split('T')[0];
                $('#returnDate').attr('min', minDate);
                $('#minReturnDate').text(tomorrow.toLocaleDateString('ms-MY'));
                
                $('#returnDate').val('');
                
                $('#proceedAction').off('click').on('click', function() {
                    const returnDate = $('#returnDate').val();
                    
                    if (!returnDate) {
                        alert('Sila pilih tarikh jangka pulang');
                        return;
                    }
                    
                    if (returnDate < minDate) {
                        alert('Tarikh jangka pulang tidak boleh sebelum esok');
                        return;
                    }
                    
                    $('#proceedModal').modal('hide');
                    
                    $.ajax({
                        url: 'request-status/' + requestID + '/proceed',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            return_date: returnDate
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#successMessage').text(response.message);
                                $('#successModal').modal('show');
                                $('#reservationTable').DataTable().ajax.reload();
                            } else {
                                $('#errorMessage').text('Gagal memproses tempahan: ' + response.message);
                                $('#errorModal').modal('show');
                            }
                        },
                        error: function(xhr) {
                            console.log('Fallback Error details:', xhr.responseText);
                            console.log('Fallback Status:', xhr.status);
                            
                            let errorMessage = 'Ralat ketika memproses tempahan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = response.message || errorMessage;
                                } catch (e) {
                                    errorMessage = xhr.responseText;
                                }
                            }
                            
                            $('#errorMessage').text(errorMessage);
                            $('#errorModal').modal('show');
                        }
                    });
                });
            }
        });
    }

    // Show success modal if session has the flag
    @if(session('show_success_modal'))
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        });
    @endif
  </script>
</body>
</html>
