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
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <style>
        #accordionSidebar { background-color: #007b78 !important; background-image: none !important; }
        .sidebar-brand-text { color: white; }
        .card { border-radius: 8px; }
        .btn-primary-custom {
            background-color: #007b78;
            color: #fff;
        }
        .btn-primary-custom:hover {
            background-color: #005f56;
            color: #fff;
        }
        
        /* Autocomplete styling */
        .ui-autocomplete {
            position: absolute;
            z-index: 1000;
            cursor: default;
            padding: 0;
            margin-top: 2px;
            list-style: none;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .ui-autocomplete > li {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }
        .ui-autocomplete > li:last-child {
            border-bottom: none;
        }
        .ui-autocomplete > li:hover,
        .ui-autocomplete > li.ui-state-focus {
            background-color: #f5f5f5;
            cursor: pointer;
        }
        .ui-helper-hidden-accessible {
            display: none;
        }
        .staff-search-container {
            position: relative;
        }
        .staff-search-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            color: #6c757d;
        }
        .staff-result-container {
        position: relative;
    }
    #staff-results {
        display: none;
        position: absolute;
        background: white;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .staff-result-item {
        cursor: pointer;
        padding: 8px 12px;
    }
    .staff-result-item:hover {
        background-color: #f5f5f5;
    }
    .btn-primary-custom {
        background-color: #4e73df;
        color: white;
    }
    .btn-primary-custom:hover {
        background-color: #2e59d9;
        color: white;
    }
        /* Staff Search Styles */
    #staff-search-results {
        position: absolute;
        width: 100%;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-height: 300px;
        overflow-y: auto;
    }

    #staff-results-list {
        margin-bottom: 0;
    }

    .staff-item {
        transition: all 0.2s;
    }

    .staff-item:hover {
        background-color: #f8f9fa;
    }

    .staff-item h6 {
        color: #007b78;
        font-weight: 600;
    }

    .staff-item small {
        color: #6c757d;
    }

    .staff-item p {
        color: #495057;
        margin-bottom: 0.25rem;
    }

    /* Clear search button */
    #clear-search {
        transition: all 0.3s;
    }

    #clear-search:hover {
        background-color: #e9ecef;
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
        <a class="nav-link collapsed active" href="#" data-toggle="collapse" data-target="#collapseComponents"
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

      <!--Manage Return -->
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
                <h1 class="h3 mb-4 text-gray-800">Permohonan Fail</h1>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm" style="background:white; border-radius:8px;">
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.submitFileRequest', $file->fileID) }}">
                                    @csrf
                                    
                                    <!-- Display validation errors -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Display success/error messages -->
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <!-- Maklumat Fail (Non-editable) -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #007b78;">
                                            <h5 class="mb-0">Maklumat Fail</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Kod Fail</label>
                                                <input type="text" class="form-control" value="{{ $file->functionCode }}-{{ $file->activityCode }}/{{ $file->subActivityCode }}/{{ $file->fileCode }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Fail</label>
                                                <input type="text" class="form-control" value="{{ $file->fileName }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <textarea class="form-control" rows="2" readonly>{{ $file->fileDescription }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Tahap Keselamatan</label>
                                                <input type="text" class="form-control" value="{{ $file->fileLevel }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <input type="text" class="form-control" value="{{ $file->fileStatus }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Maklumat Pemohon (Editable) -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #007b78;">
                                            <h5 class="mb-0">Maklumat Pemohon</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Staff Search Section -->
                                            <div class="form-group">
                                                <label>Cari Staff <small class="text-muted">(Mulai taip nama/emel/telefon)</small></label>
                                                <div class="input-group">
                                                    <input type="text" id="staff-search" class="form-control" placeholder="Cari staff...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="staff-search-results" class="mt-2 d-none">
                                                    <div class="list-group" id="staff-results-list"></div>
                                                </div>
                                            </div>

                                            <!-- Form Fields -->
                                            <div class="form-group">
                                                <label>Nama Pemohon <span class="text-danger">*</span></label>
                                                <input type="text" name="requester_name" id="requester_name" class="form-control @error('requester_name') is-invalid @enderror" value="{{ old('requester_name') }}" required>
                                                @error('requester_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Jawatan <span class="text-danger">*</span></label>
                                                <input type="text" name="requester_position" id="requester_position" class="form-control @error('requester_position') is-invalid @enderror" value="{{ old('requester_position') }}" required>
                                                @error('requester_position')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Nombor Telefon <span class="text-danger">*</span></label>
                                                <input type="text" name="requester_phone" id="requester_phone" class="form-control @error('requester_phone') is-invalid @enderror" value="{{ old('requester_phone') }}" required>
                                                @error('requester_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Emel <span class="text-danger">*</span></label>
                                                <input type="email" name="requester_email" id="requester_email" class="form-control @error('requester_email') is-invalid @enderror" value="{{ old('requester_email') }}" required>
                                                @error('requester_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Tarikh Pulang <span class="text-danger">*</span></label>
                                                <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date') }}" min="{{ date('Y-m-d') }}" required>
                                                @error('return_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary-custom" style="background-color: #007b78;">Hantar Permohonan</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                                </form>

                                <!-- Staff search results container -->
                                <div class="staff-result-container">
                                    <div id="staff-results"></div>
                                </div>

                                <!-- Success Modal -->
                                <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title" id="successModalLabel">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    Permohonan Berjaya Dihantar
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if(session('request_details'))
                                                    <div class="text-center mb-4">
                                                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                                        <h4 class="mt-3 text-success">Permohonan Berjaya!</h4>
                                                        <p class="text-muted">Permohonan fail anda telah dihantar dengan berjaya.</p>
                                                    </div>
                                                    
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h6 class="card-title text-primary">Maklumat Permohonan</h6>
                                                            <hr>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>ID Permohonan:</strong></div>
                                                                <div class="col-sm-8">#{{ session('request_details.request_id') }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Nama Pemohon:</strong></div>
                                                                <div class="col-sm-8">{{ session('request_details.requester_name') }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Kod Fail:</strong></div>
                                                                <div class="col-sm-8">{{ session('request_details.file_code') }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Nama Fail:</strong></div>
                                                                <div class="col-sm-8">{{ session('request_details.file_name') }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Tarikh Pulang:</strong></div>
                                                                <div class="col-sm-8">{{ \Carbon\Carbon::parse(session('request_details.return_date'))->format('d/m/Y') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <strong>Nota:</strong> Sila ambil fail di lokasi berikut: {{ $file->fileLocation }}dan hubungi staff untuk mengambil fail.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                                                    <i class="fas fa-check me-2"></i>
                                                    Tutup
                                                </button>
                                                <a href="{{ route($routePrefix('manageRequest')) }}" class="btn btn-primary">
                                                    <i class="fas fa-list me-2"></i>
                                                    Kembali ke Senarai
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Modal -->
                                <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="errorModalLabel">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    Ralat Permohonan
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                                                    <h4 class="mt-3 text-danger">Permohonan Gagal!</h4>
                                                    <p class="text-muted">Permohonan fail anda tidak berjaya dihantar.</p>
                                                </div>
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <span id="error-message"></span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i>
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script>
    // Show success modal if session has the flag
    @if(session('show_success_modal'))
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    @endif

    // Show error modal if there are errors
    @if($errors->any())
        $(document).ready(function() {
            $('#errorModal').modal('show');
            $('#error-message').html("@foreach($errors->all() as $error){{ $error }}<br>@endforeach");
        });
    @endif

    // Staff search functionality
    $(document).ready(function() {
        // Staff search functionality
        const staffSearch = $('#staff-search');
        const resultsContainer = $('#staff-search-results');
        const resultsList = $('#staff-results-list');
        
        // Debounce function to prevent too many AJAX calls
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Search staff when typing in search box
        staffSearch.on('input', debounce(function() {
            const searchValue = $(this).val().trim();
            
            if (searchValue.length > 2) {
                $.ajax({
                    url: '{{ route("admin.searchStaff") }}',
                    method: 'GET',
                    data: { search: searchValue },
                    beforeSend: function() {
                        resultsList.html('<div class="list-group-item text-center">Mencari...</div>');
                        resultsContainer.removeClass('d-none');
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            resultsList.empty();
                            response.forEach(function(staff) {
                                resultsList.append(`
                                    <a href="#" class="list-group-item list-group-item-action staff-item" 
                                        data-id="${staff.staffID}"
                                        data-name="${staff.staffName}"
                                        data-position="${staff.staffPosition}"
                                        data-phone="${staff.staffPhone}"
                                        data-email="${staff.staffEmail}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">${staff.staffName}</h6>
                                            <small>${staff.staffPosition}</small>
                                        </div>
                                        <p class="mb-1">${staff.staffEmail}</p>
                                        <small>${staff.staffPhone}</small>
                                    </a>
                                `);
                            });
                        } else {
                            resultsList.html('<div class="list-group-item text-center">Tiada staff ditemui</div>');
                        }
                    },
                    error: function(xhr) {
                        resultsList.html('<div class="list-group-item text-danger">Ralat ketika mencari staff</div>');
                        console.error(xhr.responseText);
                    }
                });
            } else if (searchValue.length === 0) {
                resultsContainer.addClass('d-none');
            }
        }, 300));

        // Populate fields when staff is selected
        $(document).on('click', '.staff-item', function(e) {
            e.preventDefault();
            
            $('#requester_name').val($(this).data('name'));
            $('#requester_position').val($(this).data('position'));
            $('#requester_phone').val($(this).data('phone'));
            $('#requester_email').val($(this).data('email'));
            
            // Hide results and clear search
            resultsContainer.addClass('d-none');
            staffSearch.val('');
            
            // Focus on return date field for better UX
            $('input[name="return_date"]').focus();
        });

        // Clear search
        $('#clear-search').click(function() {
            staffSearch.val('');
            resultsContainer.addClass('d-none');
        });

        // Hide results when clicking elsewhere
        $(document).click(function(e) {
            if (!$(e.target).closest('#staff-search-results').length && 
                !$(e.target).is('#staff-search') &&
                !$(e.target).is('#clear-search')) {
                resultsContainer.addClass('d-none');
            }
        });

        // Show success modal if session has the flag
        @if(session('show_success_modal'))
            $('#successModal').modal('show');
        @endif

        // Show error modal if there are errors
        @if($errors->any())
            $('#errorModal').modal('show');
            $('#error-message').html("@foreach($errors->all() as $error){{ $error }}<br>@endforeach");
        @endif
    });
</script>
</body>
</html>