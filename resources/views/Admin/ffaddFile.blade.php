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
    <title>Tambah Fail – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    
    <style>
        #accordionSidebar {
            background-color: #007b78 !important;
            background-image: none !important;
        }
        .sidebar-brand-text { 
            color: white; 
        }
        
        /* Formal Card Design */
        .formal-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header-formal {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
            padding: 1rem 1.25rem;
        }
        
        .card-body-formal {
            padding: 1.5rem;
        }
        
        /* Formal Form Controls */
        .form-control-formal {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            color: #6e707e;
            background-color: #fff;
        }
        
        .form-control-formal:focus {
            border-color: #007b78;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 120, 0.25);
            outline: 0;
        }
        
        /* Formal Labels */
        .form-label-formal {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Formal Buttons */
        .btn-formal {
            border-radius: 0.35rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            border: 1px solid transparent;
        }
        
        .btn-primary-formal {
            background-color: #007b78;
            border-color: #007b78;
            color: white;
        }
        
        .btn-primary-formal:hover {
            background-color: #006b68;
            border-color: #006b68;
            color: white;
        }
        
        .btn-success-formal {
            background-color: #1cc88a;
            border-color: #1cc88a;
            color: white;
        }
        
        .btn-success-formal:hover {
            background-color: #17a673;
            border-color: #17a673;
            color: white;
        }
        
        .btn-secondary-formal {
            background-color: #858796;
            border-color: #858796;
            color: white;
        }
        
        .btn-secondary-formal:hover {
            background-color: #717384;
            border-color: #717384;
            color: white;
        }
        
        /* Formal Icons */
        .form-icon-formal {
            color: #007b78;
            margin-right: 0.5rem;
        }
        
        /* Input Group Styling */
        .input-group-formal {
            display: flex;
            align-items: stretch;
            gap: 0.5rem;
        }
        
        .input-group-formal .form-control {
            flex: 1;
        }
        
        .combo-input-formal {
            display: flex;
            gap: 0.5rem;
        }
        
        .combo-input-formal .form-control:first-child {
            flex: 0 0 120px;
        }
        
        .combo-input-formal .form-control:last-child {
            flex: 1;
        }
        
        /* Select2 Formal Styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            height: calc(1.5em + 0.75rem + 2px);
        }
        
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #007b78;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 120, 0.25);
        }
        
        /* Page Header Formal */
        .page-header-formal {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .page-title-formal {
            font-size: 1.75rem;
            font-weight: 300;
            color: #5a5c69;
            margin: 0;
        }
        
        .page-subtitle-formal {
            font-size: 0.875rem;
            color: #858796;
            margin: 0.5rem 0 0 0;
        }
        
        /* Form Section Headers */
        .section-header-formal {
            border-left: 3px solid #007b78;
            padding-left: 0.75rem;
            margin: 1.5rem 0 1rem 0;
        }
        
        .section-title-formal {
            font-size: 1rem;
            font-weight: 600;
            color: #5a5c69;
            margin: 0;
        }
        
        /* Modal Formal Styling */
        .modal-content-formal {
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
        }
        
        .modal-header-formal {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .combo-input-formal {
                flex-direction: column;
            }
            
            .combo-input-formal .form-control:first-child {
                flex: 1;
            }
            
            .page-header-formal {
                padding: 1rem;
            }
            
            .page-title-formal {
                font-size: 1.5rem;
            }
        }
        
        /* Form Validation States */
        .is-invalid {
            border-color: #e74a3b !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.25) !important;
        }
        
        .is-valid {
            border-color: #1cc88a !important;
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25) !important;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Toastr Formal Styling */
        .toast-top-right {
            top: 20px;
            right: 20px;
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
        <a class="nav-link" href="{{ route('admin.manageRequest') }}">
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

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Header -->
          <div class="page-header-formal">
            <h1 class="page-title-formal">
              <i class="fas fa-plus-circle form-icon-formal"></i>
              Tambah Fail Baru
            </h1>
            <p class="page-subtitle-formal">Isikan maklumat fail yang diperlukan untuk menambah fail baru ke dalam sistem</p>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card formal-card">
                <div class="card-header card-header-formal">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-alt mr-2"></i>
                    Maklumat Fail
                  </h6>
                </div>
                <div class="card-body card-body-formal">
                  <form id="fileForm" action="{{ route('admin.storeFile') }}" method="POST">
                    @csrf
                    
                    <!-- Security Level Section -->
                    <div class="section-header-formal">
                      <h5 class="section-title-formal">
                        <i class="fas fa-shield-alt form-icon-formal"></i>
                        Tahap Keselamatan
                      </h5>
                    </div>
                    
                    <div class="form-group mb-4">
                      <label class="form-label-formal">Tahap Keselamatan *</label>
                      <select class="form-control form-control-formal" name="fileLevel" required>
                        <option value="">Pilih Tahap Keselamatan</option>
                        <option value="T">Terhad (T)</option>
                        <option value="S">Sulit (S)</option>
                        <option value="R">Rahsia (R)</option>
                        <option value="RB">Rahsia Besar (RB)</option>
                        <option value="NA">Tiada Klasifikasi</option>
                      </select>
                    </div>
                    
                    <!-- Classification Section -->
                    <div class="section-header-formal">
                      <h5 class="section-title-formal">
                        <i class="fas fa-sitemap form-icon-formal"></i>
                        Klasifikasi Fail
                      </h5>
                    </div>
                    
                    <!-- Function Section -->
                    <div class="form-group mb-3">
                      <label class="form-label-formal">Fungsi *</label>
                      <div class="input-group-formal">
                        <select class="form-control form-control-formal select2" id="functionCode" name="functionCode" required>
                          <option value="">Pilih Fungsi</option>
                          @foreach($functions as $function)
                            <option value="{{ $function->functionCode }}">{{ $function->functionCode }} - {{ $function->functionName }}</option>
                          @endforeach
                        </select>
                        <button type="button" class="btn btn-primary-formal btn-formal" data-toggle="modal" data-target="#addFunctionModal">
                          <i class="fas fa-plus"></i> Tambah
                        </button>
                      </div>
                    </div>
                    
                    <!-- Activity Section -->
                    <div class="form-group mb-3">
                      <label class="form-label-formal">Aktiviti *</label>
                      <div class="input-group-formal">
                        <select class="form-control form-control-formal select2" id="activityCode" name="activityCode" required disabled>
                          <option value="">Pilih Aktiviti</option>
                        </select>
                        <button type="button" class="btn btn-primary-formal btn-formal" data-toggle="modal" data-target="#addActivityModal">
                          <i class="fas fa-plus"></i> Tambah
                        </button>
                      </div>
                    </div>
                    
                    <!-- Sub Activity Section -->
                    <div class="form-group mb-4">
                      <label class="form-label-formal">Sub Aktiviti *</label>
                      <div class="input-group-formal">
                        <select class="form-control form-control-formal select2" id="subActivityCode" name="subActivityCode" required disabled>
                          <option value="">Pilih Sub Aktiviti</option>
                        </select>
                        <button type="button" class="btn btn-primary-formal btn-formal" data-toggle="modal" data-target="#addSubActivityModal">
                          <i class="fas fa-plus"></i> Tambah
                        </button>
                      </div>
                    </div>
                    
                    <!-- File Information Section -->
                    <div class="section-header-formal">
                      <h5 class="section-title-formal">
                        <i class="fas fa-file-contract form-icon-formal"></i>
                        Maklumat Fail
                      </h5>
                    </div>
                    
                    <!-- File Code and Name -->
                    <div class="form-group mb-3">
                      <label class="form-label-formal">Kod & Nama Fail *</label>
                      <div class="combo-input-formal">
                        <input type="text" class="form-control form-control-formal" name="fileCode" placeholder="Kod Fail" required>
                        <input type="text" class="form-control form-control-formal" name="fileName" placeholder="Nama Fail" required>
                      </div>
                    </div>
                    
                    <!-- Location Section -->
                    <div class="form-group mb-3">
                      <label class="form-label-formal">
                        <i class="fas fa-map-marker-alt form-icon-formal"></i>
                        Lokasi Penyimpanan *
                      </label>
                      <input type="text" class="form-control form-control-formal" name="fileLocation" placeholder="contoh: K1A1, Kabinet 2 Tingkat 3" required>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group mb-4">
                      <label class="form-label-formal">
                        <i class="fas fa-comment-alt form-icon-formal"></i>
                        Keterangan Tambahan
                      </label>
                      <textarea class="form-control form-control-formal" name="fileDescription" rows="4" placeholder="Masukkan keterangan tambahan tentang fail ini (pilihan)"></textarea>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="text-right mt-4">
                      <button type="submit" class="btn btn-success-formal btn-formal mr-2">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Fail
                      </button>
                      <a href="{{ route('admin.manageFiles') }}" class="btn btn-secondary-formal btn-formal">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                      </a>
                    </div>
                  </form>
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

  <!-- Add Function Modal -->
  <div class="modal fade" id="addFunctionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content modal-content-formal">
        <div class="modal-header modal-header-formal">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle mr-2"></i>
            Tambah Fungsi Baru
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addFunctionForm">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-code form-icon-formal"></i>
                Kod Fungsi *
              </label>
              <input type="text" class="form-control form-control-formal" id="newFunctionCode" name="functionCode" placeholder="contoh: 600" required>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-tag form-icon-formal"></i>
                Nama Fungsi *
              </label>
              <input type="text" class="form-control form-control-formal" id="newFunctionName" name="functionName" placeholder="contoh: Teknologi Maklumat" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-formal btn-formal" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>
            Batal
          </button>
          <button type="button" class="btn btn-primary-formal btn-formal" id="saveFunctionBtn">
            <i class="fas fa-save mr-2"></i>
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Activity Modal -->
  <div class="modal fade" id="addActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content modal-content-formal">
        <div class="modal-header modal-header-formal">
          <h5 class="modal-title">
            <i class="fas fa-tasks mr-2"></i>
            Tambah Aktiviti Baru
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addActivityForm">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-sitemap form-icon-formal"></i>
                Fungsi *
              </label>
              <select class="form-control form-control-formal" id="activityFunctionCode" name="functionCode" required>
                <option value="">Pilih Fungsi</option>
                @foreach($functions as $function)
                  <option value="{{ $function->functionCode }}">{{ $function->functionCode }} - {{ $function->functionName }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-code form-icon-formal"></i>
                Kod Aktiviti *
              </label>
              <input type="text" class="form-control form-control-formal" id="newActivityCode" name="activityCode" placeholder="contoh: 4" required>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-tag form-icon-formal"></i>
                Nama Aktiviti *
              </label>
              <input type="text" class="form-control form-control-formal" id="newActivityName" name="activityName" placeholder="contoh: Penyenggaraan Sistem" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-formal btn-formal" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>
            Batal
          </button>
          <button type="button" class="btn btn-primary-formal btn-formal" id="saveActivityBtn">
            <i class="fas fa-save mr-2"></i>
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Sub Activity Modal -->
  <div class="modal fade" id="addSubActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content modal-content-formal">
        <div class="modal-header modal-header-formal">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle mr-2"></i>
            Tambah Sub Aktiviti Baru
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addSubActivityForm">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-sitemap form-icon-formal"></i>
                Fungsi *
              </label>
              <select class="form-control form-control-formal" id="subActivityFunctionCode" name="functionCode" required>
                <option value="">Pilih Fungsi</option>
                @foreach($functions as $function)
                  <option value="{{ $function->functionCode }}">{{ $function->functionCode }} - {{ $function->functionName }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-tasks form-icon-formal"></i>
                Aktiviti *
              </label>
              <select class="form-control form-control-formal" id="subActivityActivityCode" name="activityCode" disabled required>
                <option value="">Pilih Aktiviti</option>
              </select>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-code form-icon-formal"></i>
                Kod Sub Aktiviti *
              </label>
              <input type="text" class="form-control form-control-formal" id="newSubActivityCode" name="subActivityCode" placeholder="contoh: 4" required>
            </div>
            <div class="form-group mb-3">
              <label class="form-label-formal">
                <i class="fas fa-tag form-icon-formal"></i>
                Nama Sub Aktiviti *
              </label>
              <input type="text" class="form-control form-control-formal" id="newSubActivityName" name="subActivityName" placeholder="contoh: Penyenggaraan Pelayan" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-formal btn-formal" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>
            Batal
          </button>
          <button type="button" class="btn btn-primary-formal btn-formal" id="saveSubActivityBtn">
            <i class="fas fa-save mr-2"></i>
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Berjaya</h5>
          <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" id="successMessage"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Error Modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Ralat</h5>
          <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" id="errorMessage"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Core JavaScript-->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>
    // Configure Toastr
    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": true,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    };
  </script>

  <script>
    $(document).ready(function() {
      // Display Laravel session messages
      @if(session('success'))
        toastr.success('{{ session("success") }}');
      @endif
      
      @if(session('error'))
        toastr.error('{{ session("error") }}');
      @endif
      
      @if($errors->any())
        @foreach($errors->all() as $error)
          toastr.error('{{ $error }}');
        @endforeach
      @endif

      // Initialize Select2 with modern styling
      $('.select2').select2({
        placeholder: 'Pilih pilihan...',
        allowClear: true,
        theme: 'default'
      });

      // Show loading state
      function showLoading(selector) {
        $(selector).prop('disabled', true);
        $(selector).html('<option value="">Loading...</option>');
      }

      // Show success message
      function showSuccess(message) {
        toastr.success(message);
      }

      // Show error message
      function showError(message) {
        toastr.error(message);
      }

      // Function selection changed - Handle both regular change and Select2 change
      $('#functionCode').on('change select2:select', function() {
        var functionCode = $(this).val();
        
        if (functionCode) {
          // Enable activity dropdown with loading state
          showLoading('#activityCode');
          $('#activityCode').prop('disabled', false);
          
          // Clear and disable sub activity dropdown
          $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>').prop('disabled', true);
          $('#subActivityCode').select2('destroy').select2({
            placeholder: 'Pilih Sub Aktiviti...',
            allowClear: true,
            theme: 'default'
          });
          
          // Fetch activities for selected function
          var getActivitiesUrl = '{{ route("admin.getActivities", "PLACEHOLDER") }}'.replace('PLACEHOLDER', functionCode);
          
          $.get(getActivitiesUrl)
            .done(function(data) {
              $('#activityCode').empty().append('<option value="">Pilih Aktiviti</option>');
              
              if (data.length > 0) {
                $.each(data, function(key, activity) {
                  $('#activityCode').append('<option value="'+activity.activityCode+'">'+activity.activityCode+' - '+activity.activityName+'</option>');
                });
                $('#activityCode').prop('disabled', false);
                
                // Reinitialize Select2 for activity dropdown
                $('#activityCode').select2('destroy').select2({
                  placeholder: 'Pilih Aktiviti...',
                  allowClear: true,
                  theme: 'default'
                });
              } else {
                $('#activityCode').append('<option value="">Tiada aktiviti tersedia</option>');
                showError('Tiada aktiviti dijumpai untuk fungsi ini');
              }
              
              // Trigger change to reset sub activity
              $('#activityCode').trigger('change');
            })
            .fail(function(xhr, status, error) {
              $('#activityCode').empty().append('<option value="">Ralat memuatkan data</option>');
              showError('Ralat semasa memuatkan senarai aktiviti');
            });
        } else {
          // Disable both activity and sub activity dropdowns
          $('#activityCode').empty().append('<option value="">Pilih Aktiviti</option>').prop('disabled', true);
          $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>').prop('disabled', true);
          
          // Reinitialize Select2
          $('#activityCode').select2('destroy').select2({
            placeholder: 'Pilih Aktiviti...',
            allowClear: true,
            theme: 'default'
          });
          $('#subActivityCode').select2('destroy').select2({
            placeholder: 'Pilih Sub Aktiviti...',
            allowClear: true,
            theme: 'default'
          });
        }
      });

      // Activity selection changed - Handle both regular change and Select2 change
      $('#activityCode').on('change select2:select', function() {
        var activityCode = $(this).val();
        var functionCode = $('#functionCode').val(); // Get the selected function code
        
        if (activityCode && functionCode) {
          // Enable sub activity dropdown with loading state
          showLoading('#subActivityCode');
          $('#subActivityCode').prop('disabled', false);
          
          // Fetch sub activities for selected activity and function
          var getSubActivitiesUrl = '{{ route("admin.getSubActivities", "PLACEHOLDER") }}'.replace('PLACEHOLDER', activityCode) + '?functionCode=' + functionCode;
          
          $.get(getSubActivitiesUrl)
            .done(function(data) {
              $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>');
              
              if (data.length > 0) {
                $.each(data, function(key, subActivity) {
                  $('#subActivityCode').append('<option value="'+subActivity.subActivityCode+'">'+subActivity.subActivityCode+' - '+subActivity.subActivityName+'</option>');
                });
                $('#subActivityCode').prop('disabled', false);
                
                // Reinitialize Select2 for sub activity dropdown
                $('#subActivityCode').select2('destroy').select2({
                  placeholder: 'Pilih Sub Aktiviti...',
                  allowClear: true,
                  theme: 'default'
                });
              } else {
                $('#subActivityCode').append('<option value="">Tiada sub aktiviti tersedia</option>');
                showError('Tiada sub aktiviti dijumpai untuk aktiviti ini');
              }
            })
            .fail(function(xhr, status, error) {
              $('#subActivityCode').empty().append('<option value="">Ralat memuatkan data</option>');
              showError('Ralat semasa memuatkan senarai sub aktiviti');
            });
        } else {
          // Disable sub activity dropdown
          $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>').prop('disabled', true);
          $('#subActivityCode').select2('destroy').select2({
            placeholder: 'Pilih Sub Aktiviti...',
            allowClear: true,
            theme: 'default'
          });
        }
      });

      // Function selection changed in sub activity modal
      $('#subActivityFunctionCode').on('change', function() {
        var functionCode = $(this).val();
        
        if (functionCode) {
          // Enable activity dropdown
          $('#subActivityActivityCode').prop('disabled', false);
          
          // Fetch activities for selected function
          var getActivitiesUrl = '{{ route("admin.getActivities", "PLACEHOLDER") }}'.replace('PLACEHOLDER', functionCode);
          $.get(getActivitiesUrl, function(data) {
            $('#subActivityActivityCode').empty().append('<option value="">Pilih Aktiviti</option>');
            
            $.each(data, function(key, activity) {
              $('#subActivityActivityCode').append('<option value="'+activity.activityCode+'">'+activity.activityCode+' - '+activity.activityName+'</option>');
            });
          });
        } else {
          // Disable activity dropdown
          $('#subActivityActivityCode').empty().append('<option value="">Pilih Aktiviti</option>').prop('disabled', true);
        }
      });

      // Save new function
      $('#saveFunctionBtn').click(function() {
        var formData = {
          functionCode: $('#newFunctionCode').val(),
          functionName: $('#newFunctionName').val(),
          _token: '{{ csrf_token() }}'
        };
        
        $.post('{{ route("admin.addFunction") }}', formData, function(response) {
          if (response.success) {
            // Add new option to function dropdown
            $('#functionCode').append('<option value="'+response.function.functionCode+'" selected>'+response.function.functionCode+' - '+response.function.functionName+'</option>');
            $('#functionCode').trigger('change');
            
            // Close modal and show success message
            $('#addFunctionModal').modal('hide');
            $('#successMessage').text('Fungsi baru berjaya ditambah.');
            $('#successModal').modal('show');
            
            // Reset form
            $('#addFunctionForm')[0].reset();
          }
        }).fail(function(xhr) {
          $('#errorMessage').text(xhr.responseJSON.message || 'Ralat berlaku semasa menambah fungsi baru.');
          $('#errorModal').modal('show');
        });
      });

      // Save new activity
      $('#saveActivityBtn').click(function() {
        var formData = {
          functionCode: $('#activityFunctionCode').val(),
          activityCode: $('#newActivityCode').val(),
          activityName: $('#newActivityName').val(),
          _token: '{{ csrf_token() }}'
        };
        
        $.post('{{ route("admin.addActivity") }}', formData, function(response) {
          if (response.success) {
            // If the activity belongs to the currently selected function, add it to the dropdown
            if ($('#functionCode').val() == formData.functionCode) {
              $('#activityCode').append('<option value="'+response.activity.activityCode+'" selected>'+response.activity.activityCode+' - '+response.activity.activityName+'</option>');
              $('#activityCode').trigger('change');
            }
            
            // Close modal and show success message
            $('#addActivityModal').modal('hide');
            $('#successMessage').text('Aktiviti baru berjaya ditambah.');
            $('#successModal').modal('show');
            
            // Reset form
            $('#addActivityForm')[0].reset();
          }
        }).fail(function(xhr) {
          $('#errorMessage').text(xhr.responseJSON.message || 'Ralat berlaku semasa menambah aktiviti baru.');
          $('#errorModal').modal('show');
        });
      });

      // Save new sub activity
      $('#saveSubActivityBtn').click(function() {
        var formData = {
          functionCode: $('#subActivityFunctionCode').val(),
          activityCode: $('#subActivityActivityCode').val(),
          subActivityCode: $('#newSubActivityCode').val(),
          subActivityName: $('#newSubActivityName').val(),
          _token: '{{ csrf_token() }}'
        };
        
        $.post('{{ route("admin.addSubActivity") }}', formData, function(response) {
          if (response.success) {
            // If the sub activity belongs to the currently selected function and activity, add it to the dropdown
            if ($('#functionCode').val() == formData.functionCode && $('#activityCode').val() == formData.activityCode) {
              $('#subActivityCode').append('<option value="'+response.subActivity.subActivityCode+'" selected>'+response.subActivity.subActivityCode+' - '+response.subActivity.subActivityName+'</option>');
            }
            
            // Close modal and show success message
            $('#addSubActivityModal').modal('hide');
            $('#successMessage').text('Sub aktiviti baru berjaya ditambah.');
            $('#successModal').modal('show');
            
            // Reset form
            $('#addSubActivityForm')[0].reset();
          }
        }).fail(function(xhr) {
          $('#errorMessage').text(xhr.responseJSON.message || 'Ralat berlaku semasa menambah sub aktiviti baru.');
          $('#errorModal').modal('show');
        });
      });

      // Form submission with validation and AJAX
      $('#fileForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        let isValid = true;
        let firstError = null;

        // Validate required fields
        $(this).find('[required]').each(function() {
          if (!$(this).val()) {
            isValid = false;
            $(this).addClass('is-invalid');
            if (!firstError) {
              firstError = $(this);
            }
          } else {
            $(this).removeClass('is-invalid');
          }
        });

        if (!isValid) {
          toastr.error('Sila lengkapkan semua medan yang diperlukan');
          if (firstError) {
            firstError.focus();
          }
          return false;
        }

        // Show loading state on submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="loading-spinner"></span> Menyimpan...');

        // Prepare form data
        const formData = new FormData(this);

        // Submit form via AJAX
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              // Show success message with Toastr
              toastr.success(response.message || 'Fail baru berjaya ditambah');
              
              // Alternative: Show success modal (uncomment if preferred)
              // $('#successMessage').text(response.message || 'Fail baru berjaya ditambah');
              // $('#successModal').modal('show');
              
              // Reset form
              $('#fileForm')[0].reset();
              
              // Reset Select2 dropdowns to initial state
              $('#functionCode').val(null).trigger('change');
              $('#activityCode').empty().append('<option value="">Pilih Aktiviti</option>').prop('disabled', true);
              $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>').prop('disabled', true);
              
              // Reinitialize Select2
              $('.select2').select2('destroy').select2({
                placeholder: 'Pilih pilihan...',
                allowClear: true,
                theme: 'default'
              });
              
              // Optional: Redirect after 2 seconds
              setTimeout(function() {
                window.location.href = '{{ route("admin.manageFiles") }}';
              }, 2000);
            } else {
              // Show error message with Toastr
              toastr.error(response.message || 'Terdapat ralat semasa menyimpan fail');
              
              // Alternative: Show error modal (uncomment if preferred)
              // $('#errorMessage').text(response.message || 'Terdapat ralat semasa menyimpan fail');
              // $('#errorModal').modal('show');
            }
          },
          error: function(xhr, status, error) {
            let errorMessage = 'Terdapat ralat semasa menyimpan fail';
            
            if (xhr.responseJSON) {
              if (xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
              } else if (xhr.responseJSON.errors) {
                // Handle validation errors
                let errorMessages = [];
                Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                  errorMessages.push(xhr.responseJSON.errors[key][0]);
                });
                errorMessage = errorMessages.join(', ');
              }
            }
            
            // Show error message with Toastr
            toastr.error(errorMessage);
            
            // Alternative: Show error modal (uncomment if preferred)
            // $('#errorMessage').text(errorMessage);
            // $('#errorModal').modal('show');
          },
          complete: function() {
            // Re-enable submit button
            submitBtn.prop('disabled', false).html(originalText);
          }
        });
      });

      // Remove validation errors on input
      $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
      });
    });
  </script>
</body>
</html>