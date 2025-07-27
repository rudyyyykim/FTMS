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
    <title>Urus Pengguna – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
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
      <li class="nav-item active">
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

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Urus Pengguna</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-user-plus fa-sm"></i> Tambah Pengguna Baru
        </button>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Senarai Pengguna Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered user-table" id="userTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>No. KP</th>
                                    <th>Email</th>
                                    <th>Peranan</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->icNumber }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role == 'Admin')
                                            <span class="badge badge-primary">{{ $user->role }}</span>
                                        @elseif($user->role == 'Pka')
                                            <span class="badge badge-warning">{{ $user->role }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $user->role }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->userStatus == 'Aktif' ? 'success' : 'danger' }}">
                                            {{ $user->userStatus }}
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-info btn-sm edit-user" 
                                                data-id="{{ $user->userID }}"
                                                data-name="{{ $user->username }}"
                                                data-ic="{{ $user->icNumber }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}"
                                                data-status="{{ $user->userStatus }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-user" 
                                                data-id="{{ $user->userID }}"
                                                data-name="{{ $user->username }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>Hak Cipta &copy; {{ config('app.name') }} {{ date('Y') }}</span>
    </div>
  </div>
</footer>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna Baru</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Penuh</label>
                        <input type="text" class="form-control" id="name" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="ic_number">No. Kad Pengenalan</label>
                        <input type="text" class="form-control" id="ic_number" name="icNumber" 
                               pattern="[0-9]{6}-[0-9]{2}-[0-9]{4}" 
                               placeholder="021108-06-0076" 
                               title="Format: XXXXXX-XX-XXXX (contoh: 021108-06-0076)"
                               maxlength="14" required>
                        <small class="form-text text-muted">Format: XXXXXX-XX-XXXX (contoh: 021108-06-0076)</small>
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                               placeholder="contoh@example.com"
                               title="Sila masukkan alamat emel yang sah" required>
                        <small class="form-text text-muted">Contoh: nama@domain.com</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Laluan</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="role">Peranan</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Pilih Peranan</option>
                            <option value="Admin">Admin</option>
                            <option value="Pka">Pka</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Kemaskini Maklumat Pengguna</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Nama Penuh</label>
                        <input type="text" class="form-control" id="edit_name" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_ic_number">No. Kad Pengenalan</label>
                        <input type="text" class="form-control" id="edit_ic_number" name="icNumber" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Alamat Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Kata Laluan (Biarkan kosong jika tidak mahu menukar)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Peranan</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Pegawai">Pegawai</option>
                            <option value="Pka">Pka</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select class="form-control" id="edit_status" name="userStatus" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Kemaskini</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Padam Pengguna</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Adakah anda pasti mahu memadam pengguna ini: <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger">Tindakan ini adalah muktamad dan tidak boleh dikembalikan.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-danger" type="submit">Padam</button>
                </div>
            </form>
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
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="successMessage"></div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Alert Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Ralat</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="errorMessage"></div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="button" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Core JavaScript-->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<!-- Custom Scripts for User Management -->
<script>
$(document).ready(function() {
    console.log("User management script initialized");

    // Debug: Verify buttons exist
    console.log("Add buttons found:", $('.edit-user').length);
    console.log("Delete buttons found:", $('.delete-user').length);

    // IC Number formatting and validation for add user form
    $('#ic_number').on('input', function() {
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
        validateICNumber(value, $(this));
    });
    
    // Prevent non-numeric input for IC (except dashes)
    $('#ic_number').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9\-]/.test(char) && e.which !== 8) {
            e.preventDefault();
        }
    });
    
    // Email validation for add user form
    $('#email').on('blur', function() {
        validateEmail($(this).val(), $(this));
    });

    // Handle Add User form submission
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Add user form submitted");
        
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        
        console.log("Submitting to:", url);
        console.log("Data:", data);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                console.log("Success:", response);
                $('#addUserModal').modal('hide');
                $('#successMessage').html(response.message);
                $('#successModal').modal('show');
                form[0].reset();
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                console.log("Error:", xhr.responseText);
                var errors = xhr.responseJSON?.errors;
                var errorMessage = xhr.responseJSON?.message || 'An error occurred';
                
                if (errors) {
                    errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '<br>';
                    });
                }
                
                $('#errorMessage').html(errorMessage);
                $('#errorModal').modal('show');
            }
        });
    });

    // Handle Edit button click - using event delegation
    $(document).on('click', '.edit-user', function() {
        console.log("Edit button clicked");
        
        var userId = $(this).data('id');
        var editUrl = "{{ route('admin.users.update', ['user' => ':id']) }}";
        editUrl = editUrl.replace(':id', userId);
        
        console.log("Edit URL:", editUrl);
        
        $('#editUserForm').attr('action', editUrl);
        $('#edit_user_id').val(userId);
        $('#edit_name').val($(this).data('name'));
        $('#edit_ic_number').val($(this).data('ic'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_role').val($(this).data('role'));
        $('#edit_status').val($(this).data('status'));
        
        $('#editUserModal').modal('show');
    });

    // Handle Edit User form submission
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Edit form submitted");
        
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        
        console.log("Submitting to:", url);
        console.log("Data:", data);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                console.log("Success:", response);
                $('#editUserModal').modal('hide');
                $('#successMessage').html(response.message);
                $('#successModal').modal('show');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                console.log("Error:", xhr.responseText);
                var errors = xhr.responseJSON?.errors;
                var errorMessage = xhr.responseJSON?.message || 'An error occurred';
                
                if (errors) {
                    errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '<br>';
                    });
                }
                
                $('#errorMessage').html(errorMessage);
                $('#errorModal').modal('show');
            }
        });
    });

    // Handle Delete button click - using event delegation
    $(document).on('click', '.delete-user', function() {
        console.log("Delete button clicked");
        
        var userId = $(this).data('id');
        var userName = $(this).data('name');
        var deleteUrl = "{{ route('admin.users.destroy', ['user' => ':id']) }}";
        deleteUrl = deleteUrl.replace(':id', userId);
        
        console.log("Delete URL:", deleteUrl);
        
        $('#deleteUserForm').attr('action', deleteUrl);
        $('#deleteUserName').text(userName);
        $('#deleteUserModal').modal('show');
    });

    // Handle Delete User form submission
    $('#deleteUserForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Delete form submitted");
        
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        
        console.log("Submitting to:", url);
        console.log("Data:", data);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                console.log("Success:", response);
                $('#deleteUserModal').modal('hide');
                $('#successMessage').html(response.message);
                $('#successModal').modal('show');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                console.log("Error:", xhr.responseText);
                $('#errorMessage').html('Ralat berlaku ketika memadam pengguna.');
                $('#errorModal').modal('show');
            }
        });
    });

    // IC Number validation with proper Malaysian format
    $('#ic_number, #edit_ic_number').on('input', function() {
        var ic = $(this).val().replace(/-/g, ''); // Remove existing hyphens
        var formatted = '';
        
        // Format as YYMMDD-PB-###G
        if (ic.length > 0) {
            formatted = ic.substring(0, 6); // First 6 digits (YYMMDD)
            
            if (ic.length > 6) {
                formatted += '-' + ic.substring(6, 8); // Add hyphen and next 2 digits (PB)
                
                if (ic.length > 8) {
                    formatted += '-' + ic.substring(8, 12); // Add hyphen and last 4 digits (###G)
                }
            }
        }
        
        $(this).val(formatted);
        
        // Optionally enforce max length
        if (ic.length > 12) {
            $(this).val(formatted.substring(0, 14)); // Max 14 characters including hyphens
        }
    });
    
    // Validation functions
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
});
</script>
</body>
</html>