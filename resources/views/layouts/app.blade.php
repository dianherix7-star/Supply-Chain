<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SCM Global') — Supply Chain Intelligence</title>
    <meta name="description" content="Global Supply Chain Risk Intelligence Platform — monitoring risiko rantai pasok global berbasis multi-API">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #dc2626; /* Red */
            --primary-hover: #b91c1c;
            --primary-light: rgba(220, 38, 38, 0.1);
            --background: #000000; /* Black */
            --sidebar-bg: #0a0a0a;
            --sidebar-border: rgba(255, 255, 255, 0.1);
            --card-bg: #121212;
            --card-border: #262626;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #262626;
            --radius-lg: 12px;
            --radius-md: 10px;
            --radius-sm: 6px;
            --transition: all 0.2s ease-in-out;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -2px rgba(0, 0, 0, 0.5);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5);
        }

        * { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            letter-spacing: -0.015em;
        }

        body { 
            background: var(--background); 
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== CUSTOM SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #3f3f46;
            border-radius: 20px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #52525b;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            color: #f1f5f9;
            overflow-y: auto;
            z-index: 1000;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.08) transparent;
            display: flex;
            flex-direction: column;
        }

        .sidebar .brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar .brand .brand-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            margin-right: 12px;
        }

        .sidebar .brand h5 {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .sidebar .brand small {
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 500;
        }

        .sidebar .nav-section {
            padding: 20px 24px 6px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #475569;
            font-weight: 700;
        }

        .sidebar a.nav-item {
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            transition: var(--transition);
            font-size: 0.85rem;
            font-weight: 600;
            margin: 2px 12px;
            border-radius: 8px;
            position: relative;
        }

        .sidebar a.nav-item:hover {
            background: rgba(255, 255, 255, 0.03);
            color: #f1f5f9;
        }

        .sidebar a.nav-item.active {
            background: rgba(255, 255, 255, 0.07);
            color: white;
        }

        .sidebar a.nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 25%;
            height: 50%;
            width: 3px;
            background-color: var(--primary);
            border-radius: 0 4px 4px 0;
        }

        .sidebar a.nav-item i {
            width: 18px;
            text-align: center;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .sidebar .badge-pill {
            margin-left: auto;
            font-size: 0.62rem;
            padding: 3px 8px;
            font-weight: 700;
            border-radius: 6px;
        }

        .sidebar .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 16px 12px;
            margin-top: auto;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            height: 64px;
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar h5 {
            margin: 0;
            font-weight: 700;
            color: var(--text-main);
            font-size: 1rem;
            letter-spacing: -0.3px;
        }

        /* ===== CARDS ===== */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            background: var(--card-bg);
            transition: var(--transition);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border-radius: var(--radius-lg);
            padding: 24px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        /* ===== BADGES ===== */
        .badge {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
        }
        .badge-risk-high   { background: #fef2f2; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.12); }
        .badge-risk-medium { background: #fffbeb; color: #d97706; border: 1px solid rgba(245, 158, 11, 0.12); }
        .badge-risk-low    { background: #f0fdf4; color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.12); }

        /* ===== TABLE ===== */
        .table {
            margin-bottom: 0;
            color: var(--text-main);
        }
        .table th { 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            background-color: var(--card-bg);
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
            white-space: nowrap;
        }
        .table td { 
            vertical-align: middle; 
            font-size: 0.85rem;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            background-color: transparent;
            white-space: nowrap;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* ===== FORM INPUTS ===== */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            font-size: 0.875rem;
            color: var(--text-main);
            background-color: var(--card-bg);
            transition: var(--transition);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            outline: none;
            background-color: var(--card-bg);
            color: var(--text-main);
        }

        /* ===== BUTTONS ===== */
        .btn {
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 8px 16px;
            transition: var(--transition);
        }
        .btn-primary {
            background-color: var(--primary);
            border: 1px solid var(--primary-hover);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: #991b1b;
            color: #ffffff;
        }
        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-main);
            background-color: transparent;
        }
        .btn-outline-secondary:hover {
            background-color: var(--border-color);
            color: var(--text-main);
            border-color: var(--text-muted);
        }

        /* ===== THEME OVERRIDES ===== */
        .text-dark { color: var(--text-main) !important; }
        .text-muted { color: var(--text-muted) !important; }
        .text-secondary { color: var(--text-muted) !important; }
        .bg-white { background-color: var(--card-bg) !important; }
        .bg-light { background-color: rgba(255, 255, 255, 0.05) !important; color: var(--text-main) !important; }
        .border { border-color: var(--border-color) !important; }
        
        /* ===== PAGINATION ===== */
        .pagination { --bs-pagination-bg: var(--card-bg); --bs-pagination-border-color: var(--border-color); --bs-pagination-color: var(--text-main); --bs-pagination-hover-bg: var(--sidebar-bg); --bs-pagination-hover-color: var(--text-main); }
        .page-link { background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-main); }
        .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: white; }
        .page-item.disabled .page-link { background-color: var(--sidebar-bg); border-color: var(--border-color); color: var(--text-muted); }

        /* ===== MISC ===== */
        .page-main { 
            padding: 32px; 
            flex-grow: 1;
        }
        .section-title { font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin-bottom: 6px; letter-spacing: -0.5px; }
        .section-sub   { font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0; }
    </style>

    @stack('styles')
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand d-flex align-items-center">
        <div class="brand-icon">🌐</div>
        <div>
            <h5>SCM Global</h5>
            <small>Supply Chain Intelligence</small>
        </div>
    </div>

    <div class="nav-section mt-1">Overview</div>

    @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard Admin
        </a>
    @else
        <a href="{{ route('user.dashboard') }}"
           class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard Saya
        </a>
    @endif

    <div class="nav-section">Master Data</div>

    <a href="{{ route('countries.index') }}"
       class="nav-item {{ request()->routeIs('countries.*') ? 'active' : '' }}">
        <i class="bi bi-globe-americas"></i> Countries
    </a>

    <a href="{{ route('ports.index') }}"
       class="nav-item {{ request()->routeIs('ports.*') ? 'active' : '' }}">
        <i class="bi bi-anchor"></i> Ports
        <span class="badge badge-pill bg-primary ms-auto">Map</span>
    </a>

    <div class="nav-section">External Data</div>

    <a href="{{ route('weather.index') }}"
       class="nav-item {{ request()->routeIs('weather.*') ? 'active' : '' }}">
        <i class="bi bi-cloud-sun"></i> Weather
    </a>

    <a href="{{ route('currency.index') }}"
       class="nav-item {{ request()->routeIs('currency.*') ? 'active' : '' }}">
        <i class="bi bi-currency-exchange"></i> Currency
    </a>

    <a href="{{ route('news.index') }}"
       class="nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i> News
    </a>

    <a href="{{ route('economic.index') }}"
       class="nav-item {{ request()->routeIs('economic.*') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow"></i> Economic Data
    </a>

    <div class="nav-section">Analytics</div>

    <a href="{{ route('risk.index') }}"
       class="nav-item {{ request()->routeIs('risk.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle"></i> Risk Score
    </a>

    <a href="{{ route('analytics.index') }}"
       class="nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i> Analytics
        <span class="badge badge-pill bg-success ms-auto">New</span>
    </a>

    <a href="{{ route('compare.index') }}"
       class="nav-item {{ request()->routeIs('compare.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i> Compare
    </a>

    <div class="nav-section">Management</div>

    <a href="{{ route('watchlist.index') }}"
       class="nav-item {{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
        <i class="bi bi-bookmark-star"></i> Watchlist
    </a>

    <a href="{{ route('articles.index') }}"
       class="nav-item {{ request()->routeIs('articles.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> Articles
    </a>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2 px-2 mb-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:32px;height:32px;font-size:14px;background:linear-gradient(135deg,#dc2626,#991b1b)!important;">
                <i class="bi bi-person-fill text-white" style="font-size:14px;"></i>
            </div>
            <div>
                <div style="font-size:0.8rem;font-weight:600;color:rgba(255,255,255,0.9);">
                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                </div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);">
                    {{ Auth::check() && Auth::user()->isAdmin() ? 'Administrator' : 'User' }}
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item w-100 border-0 text-start"
                    style="background:rgba(239,68,68,0.15);color:#f87171;">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Content -->
<div class="content">

    <div class="topbar">
        <div>
            <h5>@yield('page-title', 'Dashboard')</h5>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-0 py-1 px-3" style="font-size:0.8rem;" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-0 py-1 px-3" style="font-size:0.8rem;" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif
            <span class="text-muted" style="font-size:0.8rem;">
                <i class="bi bi-clock me-1"></i>{{ now()->format('d M Y, H:i') }}
            </span>
        </div>
    </div>

    <div class="page-main">
        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-3">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@stack('scripts')

</body>
</html>