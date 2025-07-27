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

// WhatsApp message generator function
$generateWhatsAppLink = function($file) {
    if (!$file['staffPhone']) {
        return null;
    }
    
    $message = '';
    if ($file['status'] === 'due_today') {
        $message = "Salam {$file['staffName']},\n\nAnda telah dipinjamkan fail bernombor rujukan {$file['fileCodeDisplay']}, namun sistem kami merekodkan bahawa hari ini adalah tarikh akhir pemulangan fail tersebut.\n\nSekiranya anda masih menyimpan fail ini, mohon kerjasama untuk memulangkan fail tersebut hari ini kepada PKA.\n\nJika anda tidak pasti tentang fail ini, sila hubungi pihak Pembantu Khidmat Awam untuk maklumat lanjut.\n\nTerima kasih atas kerjasama anda.";
    } else {
        $message = "Salam {$file['staffName']},\n\nAnda telah dipinjamkan fail bernombor rujukan {$file['fileCodeDisplay']}, namun sistem kami merekodkan bahawa pemulangan fail tersebut telah lewat selama {$file['daysOverdue']} hari.\n\nSekiranya anda masih menyimpan fail ini, mohon kerjasama untuk memulangkan fail tersebut secepat mungkin kepada PKA.\n\nJika anda tidak pasti tentang fail ini, sila hubungi pihak Pembantu Khidmat Awam untuk maklumat lanjut.\n\nTerima kasih atas kerjasama anda.";
    }
    
    return 'https://wa.me/' . $file['staffPhone'] . '?text=' . urlencode($message);
};
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard – {{ config('app.name') }}</title>

    <!-- Vendor CSS -->
    <link href="{{ asset('css/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- SB Admin core CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Inline Custom Sidebar Styles -->
    <style>
        /* Custom sidebar background color and no gradient */
        #accordionSidebar {
            background-color: #007b78 !important;
            background-image: none !important;
        }

        .sidebar-brand-text {
            color: white;
        }
        
        /* Custom card styling */
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card-icon {
            font-size: 1.75rem;
        }
        .card-files {
            border-left-color: #4e73df;
        }
        .card-borrowed {
            border-left-color: #f6c23e;
        }
        .card-users {
            border-left-color: #1cc88a;
        }
        
        /* Chart container */
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        /* Custom primary color */
        .text-custom-primary { color: #007B78 !important; }
        .bg-custom-primary { background-color: #007B78 !important; }
        .border-left-custom-primary { border-left: 0.25rem solid #007B78 !important; }
        
        /* Notification dropdown styles */
        .dropdown-list {
            min-width: 24rem;
            max-height: 25rem;
            overflow-y: auto;
        }
        
        .dropdown-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 800;
            color: #5a5c69;
        }
        
        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            right: 0.25rem;
            margin-top: -0.25rem;
        }
        
        /* WhatsApp icon styling */
        .whatsapp-btn {
            transition: transform 0.2s ease-in-out;
        }
        
        .whatsapp-btn:hover {
            transform: scale(1.1);
        }
        
        .whatsapp-btn img {
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
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
      <li class="nav-item active">
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
            
            <!-- Alerts Dropdown -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                @if($overdueCount > 0)
                  <span class="badge badge-danger badge-counter">{{ $overdueCount > 99 ? '99+' : $overdueCount }}</span>
                @endif
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                   aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Amaran
                </h6>
                @if($overdueCount > 0)
                  @foreach($allOverdueFiles as $overdue)
                    <div class="dropdown-item d-flex align-items-center" style="white-space: normal; padding: 0.75rem 1.5rem;">
                      <div class="mr-3">
                        <div class="icon-circle {{ $overdue['status'] === 'due_today' ? 'bg-info' : 'bg-warning' }}">
                          <i class="fas {{ $overdue['status'] === 'due_today' ? 'fa-clock' : 'fa-exclamation-triangle' }} text-white"></i>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <div class="small text-gray-500">{{ $overdue['returnDate']->format('d M Y') }}</div>
                        <span class="font-weight-bold">{{ $overdue['fileCodeDisplay'] }}</span>
                        <div class="small">Dipinjam oleh: {{ $overdue['staffName'] }}</div>
                        <div class="small {{ $overdue['status'] === 'due_today' ? 'text-info' : 'text-danger' }}">
                          @if($overdue['status'] === 'due_today')
                            Perlu dipulangkan hari ini
                          @else
                            Lewat {{ $overdue['daysOverdue'] }} hari
                          @endif
                        </div>
                      </div>
                      @if($generateWhatsAppLink($overdue))
                        <div class="ml-2">
                          <a href="{{ $generateWhatsAppLink($overdue) }}" 
                             target="_blank" 
                             class="btn btn-sm btn-outline-success p-1 whatsapp-btn"
                             title="{{ $overdue['status'] === 'due_today' ? 'Hantar peringatan WhatsApp (Hari Ini)' : 'Hantar peringatan WhatsApp (Lewat)' }}"
                             onclick="event.stopPropagation();">
                            <img src="{{ asset('images/whatsapp-icon.svg') }}" 
                                 alt="WhatsApp" 
                                 style="width: 18px; height: 18px;">
                          </a>
                        </div>
                      @else
                        <div class="ml-2">
                          <span class="text-muted small" title="Nombor telefon tiada">
                            <i class="fas fa-phone-slash" style="font-size: 14px;"></i>
                          </span>
                        </div>
                      @endif
                    </div>
                  @endforeach
                @else
                  <a class="dropdown-item text-center small text-gray-500" href="#">Tiada amaran</a>
                @endif
                <a class="dropdown-item text-center small text-gray-500" href="#">Lihat Semua Amaran</a>
              </div>
            </li>

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
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Total Files Card -->
            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card stat-card card-files shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Jumlah Fail</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFiles }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-folder stat-card-icon text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Borrowed Files Card -->
            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card stat-card card-borrowed shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Fail Dipinjam</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $borrowedFiles }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-exchange-alt stat-card-icon text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Registered Users Card -->
            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card stat-card card-users shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Pengguna Berdaftar</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users stat-card-icon text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Due Today Files Alert -->
          @if($dueTodayFiles && count($dueTodayFiles) > 0)
          <div class="row">
            <div class="col-12">
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h4 class="alert-heading"><i class="fas fa-clock"></i> Amaran: Fail Perlu Dipulangkan Hari Ini!</h4>
                <p>Terdapat <strong>{{ count($dueTodayFiles) }}</strong> fail yang perlu dipulangkan hari ini:</p>
                <hr>
                <ul class="mb-0">
                  @foreach($dueTodayFiles as $dueToday)
                    <li class="d-flex align-items-center justify-content-between mb-2">
                      <div>
                        <strong>{{ $dueToday['fileCodeDisplay'] }}</strong> - Dipinjam oleh: {{ $dueToday['staffName'] }} 
                        <span class="badge badge-info ml-2">Perlu dipulangkan hari ini</span>
                      </div>
                      @if($generateWhatsAppLink($dueToday))
                        <a href="{{ $generateWhatsAppLink($dueToday) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-success ml-2 whatsapp-btn"
                           title="Hantar peringatan WhatsApp (Hari Ini)">
                          <img src="{{ asset('images/whatsapp-icon.svg') }}" 
                               alt="WhatsApp" 
                               style="width: 20px; height: 20px;">
                        </a>
                      @else
                        <span class="text-muted ml-2" title="Nombor telefon tiada">
                          <i class="fas fa-phone-slash"></i>
                        </span>
                      @endif
                    </li>
                  @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          @endif

          <!-- Overdue Files Alert -->
          @if($overdueFiles && count($overdueFiles) > 0)
          <div class="row">
            <div class="col-12">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Amaran: Fail Tertunggak!</h4>
                <p>Terdapat <strong>{{ count($overdueFiles) }}</strong> fail yang sudah melepasi tarikh pemulangan:</p>
                <hr>
                <ul class="mb-0">
                  @foreach($overdueFiles as $overdue)
                    <li class="d-flex align-items-center justify-content-between mb-2">
                      <div>
                        <strong>{{ $overdue['fileCodeDisplay'] }}</strong> - Dipinjam oleh: {{ $overdue['staffName'] }} 
                        <span class="badge badge-danger ml-2">Lewat {{ $overdue['daysOverdue'] }} hari</span>
                      </div>
                      @if($generateWhatsAppLink($overdue))
                        <a href="{{ $generateWhatsAppLink($overdue) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-success ml-2 whatsapp-btn"
                           title="Hantar peringatan WhatsApp (Lewat)">
                          <img src="{{ asset('images/whatsapp-icon.svg') }}" 
                               alt="WhatsApp" 
                               style="width: 20px; height: 20px;">
                        </a>
                      @else
                        <span class="text-muted ml-2" title="Nombor telefon tiada">
                          <i class="fas fa-phone-slash"></i>
                        </span>
                      @endif
                    </li>
                  @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          @endif

          <!-- Chart Row -->
          <div class="row">
              <div class="col-12">
                  <div class="card shadow mb-4">
                      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                          <h6 class="m-0 font-weight-bold text-custom-primary">Statistik Permohonan Fail Mingguan</h6>
                      </div>
                      <div class="card-body">
                          <div class="chart-area" style="height: 400px;">
                              <canvas id="weeklyBorrowedChart"></canvas>
                          </div>
                          <div class="mt-4 text-center small">
                              <span class="mr-2">
                                  <i class="fas fa-circle text-primary"></i> Jumlah Fail Dipohon
                              </span>
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

  <!-- Core JavaScript-->
  <script src="{{ asset('js/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

  <!-- Replace your current Chart.js script with this CDN version -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
// Weekly Borrowed Files Chart - Area Chart
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById("weeklyBorrowedChart");
    if (ctx) {
        // Get the weekly data from the controller
        var weeklyData = @json($weeklyData);
        
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ["Isnin", "Selasa", "Rabu", "Khamis", "Jumaat"],
                datasets: [{
                    label: "Fail Dipinjam",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 4,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(255, 255, 255, 1)",
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(255, 255, 255, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: weeklyData,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' fail';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                }
            }
        });
    }
});
</script>
</body>
</html>