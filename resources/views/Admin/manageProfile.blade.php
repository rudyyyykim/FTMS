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
    <title>Urus Profil – {{ config('app.name') }}</title>
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        #accordionSidebar { background-color: #007b78 !important; background-image: none !important; }
        .sidebar-brand-text { color: white; }
        .card { border-radius: 8px; }
        .btn-primary-custom {
            background-color: #007b78;
            color: #fff;
            border-color: #007b78;
        }
        .btn-primary-custom:hover {
            background-color: #005f56;
            color: #fff;
            border-color: #005f56;
        }
        .profile-picture-container {
            position: relative;
            display: inline-block;
        }
        .profile-picture-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007b78;
        }
        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .profile-picture-container:hover .profile-picture-overlay {
            display: flex;
        }
        .remove-picture-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 12px;
            cursor: pointer;
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
        <a class="nav-link" href="{{ route($routePrefix('manageUser')) }}">
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
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ $user->username }}</span>
                            @if($user->profilePicture)
                                <img class="img-profile rounded-circle" src="{{ asset('images/' . $user->profilePicture) }}" style="width: 30px; height: 30px; object-fit: cover;">
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
            
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Urus Profil</h1>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm" style="background:white; border-radius:8px;">
                            <div class="card-body">
                                
                                <!-- Display validation errors -->
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
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

                                <form method="POST" action="{{ route($routePrefix('updateProfile')) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <!-- Profile Picture Section -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #007b78;">
                                            <h5 class="mb-0">Gambar Profil</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="profile-picture-container">
                                                @if($user->profilePicture)
                                                    <img id="profilePreview" src="{{ asset('images/' . $user->profilePicture) }}" 
                                                         alt="Profile Picture" class="profile-picture-preview">
                                                    <button type="button" class="remove-picture-btn" id="removePictureBtn" title="Buang gambar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @else
                                                    <img id="profilePreview" src="{{ asset('img/undraw_profile.svg') }}" 
                                                         alt="Default Profile" class="profile-picture-preview">
                                                @endif
                                                <div class="profile-picture-overlay" onclick="document.getElementById('profilePictureInput').click()">
                                                    <i class="fas fa-camera text-white" style="font-size: 24px;"></i>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <input type="file" id="profilePictureInput" name="profilePicture" 
                                                       accept="image/*" style="display: none;" onchange="previewImage(this)">
                                                <button type="button" class="btn btn-primary-custom btn-sm" 
                                                        onclick="document.getElementById('profilePictureInput').click()">
                                                    <i class="fas fa-upload mr-2"></i>Pilih Gambar
                                                </button>
                                                <small class="text-muted d-block mt-1">
                                                    Format yang disokong: JPEG, PNG, JPG, GIF (Maksimum: 2MB)
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #007b78;">
                                            <h5 class="mb-0">Maklumat Peribadi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>ID Pengguna</label>
                                                        <input type="text" class="form-control" value="{{ $user->userID }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nama Pengguna <span class="text-danger">*</span></label>
                                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                                               value="{{ old('username', $user->username) }}" required>
                                                        @error('username')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nombor IC <span class="text-danger">*</span></label>
                                                        <input type="text" name="icNumber" class="form-control @error('icNumber') is-invalid @enderror" 
                                                               value="{{ old('icNumber', $user->icNumber) }}" 
                                                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" 
                                                               placeholder="021108-06-0076"
                                                               title="Format: XXXXXX-XX-XXXX (contoh: 021108-06-0076)"
                                                               maxlength="14" required>
                                                        <small class="form-text text-muted">Format: XXXXXX-XX-XXXX</small>
                                                        @error('icNumber')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Emel <span class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                               value="{{ old('email', $user->email) }}" 
                                                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                               placeholder="contoh@example.com"
                                                               title="Sila masukkan alamat emel yang sah"
                                                               required>
                                                        <small class="form-text text-muted">Contoh: nama@domain.com</small>
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Peranan</label>
                                                        <input type="text" class="form-control" value="{{ $user->role }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <input type="text" class="form-control" value="{{ $user->userStatus }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Section -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background-color: #007b78;">
                                            <h5 class="mb-0">Tukar Kata Laluan</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-3">Biarkan kosong jika tidak mahu mengubah kata laluan</p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kata Laluan Baru</label>
                                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                                               minlength="8">
                                                        @error('password')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sahkan Kata Laluan Baru</label>
                                                        <input type="password" name="password_confirmation" class="form-control" minlength="8">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="fas fa-save mr-2"></i>Kemaskini Profil
                                        </button>
                                        <a href="{{ route($routePrefix('dashboard')) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                                        </a>
                                    </div>
                                </form>
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

<script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

<script>
// Preview uploaded image
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#profilePreview').attr('src', e.target.result);
            // Show remove button if there wasn't one before
            if (!$('#removePictureBtn').length) {
                $('.profile-picture-container').append('<button type="button" class="remove-picture-btn" id="removePictureBtn" title="Buang gambar"><i class="fas fa-times"></i></button>');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// IC Number formatting and validation
$(document).ready(function() {
    // Format IC number as user types
    $('input[name="icNumber"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        
        // Auto-format with dashes
        if (value.length >= 6) {
            value = value.substring(0, 6) + '-' + value.substring(6);
        }
        if (value.length >= 9) {
            value = value.substring(0, 9) + '-' + value.substring(9);
        }
        
        // Limit to 14 characters (including dashes)
        if (value.length > 14) {
            value = value.substring(0, 14);
        }
        
        $(this).val(value);
        
        // Real-time validation
        validateICNumber(value, $(this));
    });
    
    // Prevent non-numeric input for IC (except dashes)
    $('input[name="icNumber"]').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9\-]/.test(char) && e.which !== 8) {
            e.preventDefault();
        }
    });
    
    // Email validation
    $('input[name="email"]').on('blur', function() {
        validateEmail($(this).val(), $(this));
    });
});

function validateICNumber(icNumber, inputElement) {
    const icPattern = /^[0-9]{6}-[0-9]{2}-[0-9]{4}$/;
    const isValid = icPattern.test(icNumber);
    
    if (icNumber.length > 0 && !isValid) {
        inputElement.addClass('is-invalid').removeClass('is-valid');
        // Add custom error message if doesn't exist
        if (!inputElement.siblings('.invalid-feedback.ic-error').length) {
            inputElement.after('<div class="invalid-feedback ic-error">Format IC tidak sah. Gunakan format: XXXXXX-XX-XXXX</div>');
        }
    } else if (isValid) {
        inputElement.addClass('is-valid').removeClass('is-invalid');
        inputElement.siblings('.invalid-feedback.ic-error').remove();
    } else {
        inputElement.removeClass('is-invalid is-valid');
        inputElement.siblings('.invalid-feedback.ic-error').remove();
    }
}

function validateEmail(email, inputElement) {
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const isValid = emailPattern.test(email);
    
    if (email.length > 0 && !isValid) {
        inputElement.addClass('is-invalid').removeClass('is-valid');
        // Add custom error message if doesn't exist
        if (!inputElement.siblings('.invalid-feedback.email-error').length) {
            inputElement.after('<div class="invalid-feedback email-error">Format emel tidak sah. Contoh: nama@domain.com</div>');
        }
    } else if (isValid) {
        inputElement.addClass('is-valid').removeClass('is-invalid');
        inputElement.siblings('.invalid-feedback.email-error').remove();
    } else {
        inputElement.removeClass('is-invalid is-valid');
        inputElement.siblings('.invalid-feedback.email-error').remove();
    }
}

// Remove profile picture
$(document).on('click', '#removePictureBtn', function(e) {
    e.preventDefault();
    
    if (confirm('Adakah anda pasti mahu membuang gambar profil?')) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: '{{ route($routePrefix('removeProfilePicture')) }}',
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $('#profilePreview').attr('src', '{{ asset("img/undraw_profile.svg") }}');
                    $('#removePictureBtn').remove();
                    $('#profilePictureInput').val('');
                    
                    // Show success message
                    $('.card-body').prepend('<div class="alert alert-success alert-dismissible fade show" role="alert">Gambar profil berjaya dibuang!<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                }
            },
            error: function() {
                alert('Ralat semasa membuang gambar profil.');
            }
        });
    }
});

// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
</body>
</html>
