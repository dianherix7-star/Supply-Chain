<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #0d6efd;
            color: white;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }

        .sidebar .brand h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar .brand small {
            opacity: 0.75;
            font-size: 0.75rem;
        }

        .sidebar .nav-section {
            padding: 10px 15px 5px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.6;
            font-weight: 600;
        }

        .sidebar a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            transition: all 0.2s;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
        }

        .sidebar a i {
            width: 18px;
            text-align: center;
        }

        .sidebar .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .badge-pill {
            border-radius: 999px;
        }
    </style>

    @stack('styles')
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">

    <div class="brand">
        <h5><i class="bi bi-globe-americas me-2"></i>SCM Global</h5>
        <small>Supply Chain Management</small>
    </div>

    <div class="nav-section mt-2">Main Menu</div>

    <a href="{{ route('admin.dashboard') }}"
       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="{{ route('countries.index') }}"
       class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">
        <i class="bi bi-globe"></i> Countries
    </a>

    <div class="nav-section">External Data</div>

    <a href="{{ route('weather.index') }}"
       class="{{ request()->routeIs('weather.*') ? 'active' : '' }}">
        <i class="bi bi-cloud-sun"></i> Weather
    </a>

    <a href="#" class="{{ request()->routeIs('currency.*') ? 'active' : '' }}">
        <i class="bi bi-currency-exchange"></i> Currency
        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem">Soon</span>
    </a>

    <a href="#" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i> News
        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem">Soon</span>
    </a>

    <div class="nav-section">Analytics</div>

    <a href="#" class="{{ request()->routeIs('risk.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle"></i> Risk Score
        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem">Soon</span>
    </a>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                style="background:none;border:none;width:100%;color:rgba(255,255,255,0.85);
                       display:flex;align-items:center;gap:10px;padding:14px 20px;
                       font-size:0.9rem;cursor:pointer;transition:all 0.2s;border-left:3px solid transparent;"
                onmouseover="this.style.background='rgba(255,255,255,0.15)';this.style.color='white'"
                onmouseout="this.style.background='none';this.style.color='rgba(255,255,255,0.85)'">
                <i class="bi bi-box-arrow-right" style="width:18px;text-align:center;"></i>
                Logout
            </button>
        </form>
    </div>

</div>

<!-- Content -->
<div class="content">

    <div class="topbar">
        <h5>@yield('page-title', 'Supply Chain Management')</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted" style="font-size:0.85rem;">
                <i class="bi bi-person-circle me-1"></i>
                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
            </span>
        </div>
    </div>

    <div class="container-fluid p-4">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>