@extends('layouts.app')

@section('page-title', 'Dashboard Admin')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">📊 Dashboard Admin</h4>
    <small class="text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Berikut ringkasan data sistem.</small>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-globe text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Negara</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_countries'] }}</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('countries.index') }}" class="small text-primary text-decoration-none">
                    Lihat semua →
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #0dcaf0;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="bi bi-cloud-sun text-info fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Data Cuaca</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_weather'] }}</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <span class="small text-muted">
                    <span class="badge bg-warning text-dark">Segera</span> Tahap 4
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-newspaper text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Berita</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_news'] }}</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <span class="small text-muted">
                    <span class="badge bg-warning text-dark">Segera</span> Tahap 6
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                    <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Risk Score</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_risk'] }}</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <span class="small text-muted">
                    <span class="badge bg-warning text-dark">Segera</span> Tahap 7
                </span>
            </div>
        </div>
    </div>

</div>

<!-- Risk Level Summary (tampil jika ada data) -->
@if($stats['total_risk'] > 0)
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center" style="border-top: 3px solid #dc3545;">
            <div class="card-body">
                <div class="text-danger fw-bold fs-1">{{ $stats['high_risk'] }}</div>
                <div class="text-muted small">🔴 High Risk</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center" style="border-top: 3px solid #ffc107;">
            <div class="card-body">
                <div class="text-warning fw-bold fs-1">{{ $stats['medium_risk'] }}</div>
                <div class="text-muted small">🟡 Medium Risk</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center" style="border-top: 3px solid #198754;">
            <div class="card-body">
                <div class="text-success fw-bold fs-1">{{ $stats['low_risk'] }}</div>
                <div class="text-muted small">🟢 Low Risk</div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="card">
    <div class="card-header fw-semibold bg-light">
        ⚡ Quick Actions
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-auto">
                <a href="{{ route('countries.import') }}" class="btn btn-outline-success">
                    <i class="bi bi-cloud-download me-1"></i> Import Countries
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('countries.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-globe me-1"></i> Kelola Countries
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('countries.create') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Country
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Progress Tahap -->
<div class="card mt-3">
    <div class="card-header fw-semibold bg-light">📋 Progress Pengerjaan</div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-success bg-opacity-10">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small">Tahap 1: Authentication</span>
                    <span class="badge bg-success ms-auto">Selesai</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-success bg-opacity-10">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small">Tahap 2: CRUD Country</span>
                    <span class="badge bg-success ms-auto">Selesai</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-primary bg-opacity-10">
                    <i class="bi bi-arrow-repeat text-primary"></i>
                    <span class="small">Tahap 3: Import REST Countries API</span>
                    <span class="badge bg-primary ms-auto">Selesai ✓</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-light">
                    <i class="bi bi-hourglass text-muted"></i>
                    <span class="small text-muted">Tahap 4: Weather API</span>
                    <span class="badge bg-secondary ms-auto">Belum</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-light">
                    <i class="bi bi-hourglass text-muted"></i>
                    <span class="small text-muted">Tahap 5: Currency API</span>
                    <span class="badge bg-secondary ms-auto">Belum</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-light">
                    <i class="bi bi-hourglass text-muted"></i>
                    <span class="small text-muted">Tahap 6: News API</span>
                    <span class="badge bg-secondary ms-auto">Belum</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-light">
                    <i class="bi bi-hourglass text-muted"></i>
                    <span class="small text-muted">Tahap 7: Risk Score Dashboard</span>
                    <span class="badge bg-secondary ms-auto">Belum</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection