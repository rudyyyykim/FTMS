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
    <title>Edit Fail – {{ config('app.name') }}</title>

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
        .sidebar-brand-text { color: white; }
        .code-input-container {
            display: flex;
            align-items: center;
        }
        .code-input-container select {
            flex: 1;
            margin-right: 5px;
        }
        .code-input-container input {
            flex: 2;
        }
        .full-width-dropdown {
            width: calc(100% - 110px);
        }
        .combo-input {
            display: flex;
        }
        .combo-input select {
            width: 100px;
            margin-right: 10px;
        }
        .combo-input input {
            flex-grow: 1;
        }
        .file-reference {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
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
        
        /* Toastr Formal Positioning */
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

      <!--Manage Request -->
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
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit Fail</h1>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Maklumat Fail</h6>
                </div>
                <div class="card-body">
                  <!-- Current File Information -->
                  <div class="file-reference">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Fail Semasa:</strong> 
                    {{ $file->functionCode }}-{{ $file->activityCode }}/{{ $file->subActivityCode }}/{{ $file->fileCode }} - {{ $file->fileName }}
                  </div>
                  
                  <form id="fileForm" action="{{ route('admin.updateFile', $file->fileID) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Security Level -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Tahap Keselamatan*</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="fileLevel" required>
                          <option value="">Pilih Tahap</option>
                          <option value="T" {{ $file->fileLevel == 'T' ? 'selected' : '' }}>Terhad (T)</option>
                          <option value="S" {{ $file->fileLevel == 'S' ? 'selected' : '' }}>Sulit (S)</option>
                          <option value="R" {{ $file->fileLevel == 'R' ? 'selected' : '' }}>Rahsia (R)</option>
                          <option value="RB" {{ $file->fileLevel == 'RB' ? 'selected' : '' }}>Rahsia Besar (RB)</option>
                          <option value="NA" {{ ($file->fileLevel == 'NA' || $file->fileLevel == 'Biasa') ? 'selected' : '' }}>Tiada Klasifikasi</option>
                        </select>
                      </div>
                    </div>
                    
                    <!-- Function Section -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Fungsi*</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="functionCode" name="functionCode" required>
                          <option value="">Pilih Fungsi</option>
                          @foreach($functions as $func)
                            <option value="{{ $func->functionCode }}" 
                              {{ $file->functionCode == $func->functionCode ? 'selected' : '' }}>
                              {{ $func->functionCode }} - {{ $func->functionName }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    
                    <!-- Activity Section -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Aktiviti*</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="activityCode" name="activityCode" required>
                          <option value="">Pilih Aktiviti</option>
                          @foreach($activities as $act)
                            <option value="{{ $act->activityCode }}" 
                              {{ $file->activityCode == $act->activityCode ? 'selected' : '' }}>
                              {{ $act->activityCode }} - {{ $act->activityName }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    
                    <!-- Sub Activity Section -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Sub Aktiviti*</label>
                      <div class="col-sm-10">
                        <select class="form-control select2" id="subActivityCode" name="subActivityCode" required>
                          <option value="">Pilih Sub Aktiviti</option>
                          @foreach($subActivities as $subAct)
                            <option value="{{ $subAct->subActivityCode }}" 
                              {{ $file->subActivityCode == $subAct->subActivityCode ? 'selected' : '' }}>
                              {{ $subAct->subActivityCode }} - {{ $subAct->subActivityName }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    
                    <!-- File Section -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Fail*</label>
                      <div class="col-sm-10">
                        <div class="combo-input">
                          <input type="text" class="form-control" name="fileCode" value="{{ $file->fileCode }}" placeholder="Kod Fail" required>
                          <input type="text" class="form-control" name="fileName" value="{{ $file->fileName }}" placeholder="Nama Fail" required>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Location Section -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Lokasi Penyimpanan*</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="fileLocation" value="{{ $file->fileLocation }}" placeholder="cth: K1A1" required>
                      </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Keterangan</label>
                      <div class="col-sm-10">
                        <textarea class="form-control" name="fileDescription" rows="3">{{ $file->fileDescription }}</textarea>
                      </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary mr-2">
                          <i class="fas fa-save mr-1"></i> Kemaskini Fail
                        </button>
                        <a href="{{ route('admin.manageFiles') }}" class="btn btn-secondary">
                          <i class="fas fa-times mr-1"></i> Batal
                        </a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
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

    $(document).ready(function() {
      // Initialize Select2
      $('.select2').select2({
        placeholder: 'Pilih pilihan',
        allowClear: true
      });

      // Function selection changed
      $('#functionCode').on('change', function() {
        var functionCode = $(this).val();
        
        if (functionCode) {
          // Fetch activities for selected function
          var getActivitiesUrl = '{{ route("admin.getActivities", "PLACEHOLDER") }}'.replace('PLACEHOLDER', functionCode);
          $.get(getActivitiesUrl)
            .done(function(data) {
              $('#activityCode').empty().append('<option value="">Pilih Aktiviti</option>');
              
              if (data.length > 0) {
                $.each(data, function(key, activity) {
                  $('#activityCode').append('<option value="'+activity.activityCode+'">'+activity.activityCode+' - '+activity.activityName+'</option>');
                });
              } else {
                $('#activityCode').append('<option value="">Tiada aktiviti tersedia</option>');
                toastr.error('Tiada aktiviti dijumpai untuk fungsi ini');
              }
              
              // Trigger change to reset sub activity
              $('#activityCode').trigger('change');
            })
            .fail(function(xhr, status, error) {
              $('#activityCode').empty().append('<option value="">Ralat memuatkan data</option>');
              toastr.error('Ralat semasa memuatkan senarai aktiviti');
            });
        } else {
          // Disable both activity and sub activity dropdowns
          $('#activityCode').empty().append('<option value="">Pilih Aktiviti</option>');
          $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>');
        }
      });

      // Activity selection changed
      $('#activityCode').on('change', function() {
        var activityCode = $(this).val();
        var functionCode = $('#functionCode').val(); // Get the selected function code
        
        if (activityCode && functionCode) {
          // Fetch sub activities for selected activity and function
          var getSubActivitiesUrl = '{{ route("admin.getSubActivities", "PLACEHOLDER") }}'.replace('PLACEHOLDER', activityCode) + '?functionCode=' + functionCode;
          $.get(getSubActivitiesUrl)
            .done(function(data) {
              $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>');
              
              if (data.length > 0) {
                $.each(data, function(key, subActivity) {
                  $('#subActivityCode').append('<option value="'+subActivity.subActivityCode+'">'+subActivity.subActivityCode+' - '+subActivity.subActivityName+'</option>');
                });
              } else {
                $('#subActivityCode').append('<option value="">Tiada sub aktiviti tersedia</option>');
                toastr.error('Tiada sub aktiviti dijumpai untuk aktiviti ini');
              }
            })
            .fail(function(xhr, status, error) {
              $('#subActivityCode').empty().append('<option value="">Ralat memuatkan data</option>');
              toastr.error('Ralat semasa memuatkan senarai sub aktiviti');
            });
        } else {
          // Disable sub activity dropdown
          $('#subActivityCode').empty().append('<option value="">Pilih Sub Aktiviti</option>');
        }
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
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengemaskini...');

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
              toastr.success(response.message || 'Fail berjaya dikemaskini');
              
              // Optional: Redirect after 2 seconds
              setTimeout(function() {
                window.location.href = '{{ route("admin.manageFiles") }}';
              }, 2000);
            } else {
              // Show error message with Toastr
              toastr.error(response.message || 'Terdapat ralat semasa mengemaskini fail');
            }
          },
          error: function(xhr, status, error) {
            let errorMessage = 'Terdapat ralat semasa mengemaskini fail';
            
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