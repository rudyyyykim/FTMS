
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard – {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .custom-sidebar { background-color: #007B78 !important; }
        .btn-custom {
            background-color: #007B78 !important;
            border-color: #007B78 !important;
            color: white !important;
        }
        .btn-custom:hover {
            background-color: #005f59 !important;
            border-color: #005f59 !important;
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">

<!-- SIDEBAR -->
<ul class="navbar-nav sidebar sidebar-dark custom-sidebar accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-start pl-3" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('images/jupem-logo.jpg') }}" alt="Jupem Logo"> style="width: 30px;">
        </div>
        <div class="sidebar-brand-text mx-3">FTMS Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/arrangeDelivery') }}" class="nav-link">
            <i class="nav-icon fas fa-truck"></i>
            <p>Manage Files</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>
        <div id="collapseUsers" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('') }}">Manage User</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('') }}">
            <i class="fas fa-eye"></i>
            <span>View Requests</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('') }}">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('') }}">
            <i class="fas fa-cogs"></i>
            <span>Settings</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
</ul>
<!-- END SIDEBAR -->

<!-- CONTENT -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content" class="p-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 text-gray-800">Dashboard</h1>
            <div>
                <a href="{{ route('policy') }}" class="btn btn-custom btn-sm">📄 View All Requests</a>
                <a href="{{ route('policy') }}" class="btn btn-secondary btn-sm">📁 Manage Files</a>
            </div>
        </div>

        <!-- KPI CARDS -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Files</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">1,230</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Borrowed Files</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">86</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-left-danger shadow py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue Files</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">File Request Trends</h6>
            </div>
            <div class="card-body">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>
</div>

</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<script>
var ctx = document.getElementById("myAreaChart").getContext("2d");
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri"],
        datasets: [{
            label: "Requests",
            data: [12, 19, 15, 23, 17],
            backgroundColor: "rgba(0,123,120,0.1)",
            borderColor: "#007B78",
            pointBackgroundColor: "#007B78",
            pointBorderColor: "#007B78",
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>
