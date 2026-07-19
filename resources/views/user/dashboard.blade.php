@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('page-title', 'Dashboard Pengguna')

@push('styles')
<style>
    /* Custom User Dashboard Styles */
    .user-stat-card {
        border-radius: var(--radius-lg);
        background: #ffffff;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        position: relative;
    }
    .user-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .user-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-top-left-radius: var(--radius-lg);
        border-top-right-radius: var(--radius-lg);
    }
    .stat-watchlist::before { background: linear-gradient(90deg, #6366f1, #3b82f6); }
    .stat-high::before      { background: linear-gradient(90deg, #f43f5e, #e11d48); }
    .stat-medium::before    { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .stat-low::before       { background: linear-gradient(90deg, #10b981, #059669); }

    /* Custom Watchlist Grid Item */
    .watchlist-card {
        border-radius: var(--radius-lg);
        background: #ffffff;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        overflow: hidden;
    }
    .watchlist-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    .watchlist-header {
        padding: 20px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .watchlist-body {
        padding: 20px;
    }
</style>
@endpush

@section('content')

<div class="mb-4">
    <h1 class="section-title">👋 Selamat Datang, {{ Auth::user() ? Auth::user()->name : 'User' }}!</h1>
    <p class="section-sub">Pantau kondisi dan tingkat risiko negara-negara watchlist Anda</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="user-stat-card stat-watchlist p-3 text-center">
            <div class="text-primary fw-extrabold fs-2" style="font-weight: 800;">{{ $stats['total_watchlist'] }}</div>
            <div class="text-muted small fw-semibold">Negara Dipantau</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="user-stat-card stat-high p-3 text-center">
            <div class="text-danger fw-extrabold fs-2" style="font-weight: 800;">{{ $stats['high_risk'] }}</div>
            <div class="text-muted small fw-semibold">High Risk</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="user-stat-card stat-medium p-3 text-center">
            <div class="text-warning fw-extrabold fs-2" style="font-weight: 800;">{{ $stats['medium_risk'] }}</div>
            <div class="text-muted small fw-semibold">Medium Risk</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="user-stat-card stat-low p-3 text-center">
            <div class="text-success fw-extrabold fs-2" style="font-weight: 800;">{{ $stats['low_risk'] }}</div>
            <div class="text-muted small fw-semibold">Low Risk</div>
        </div>
    </div>
</div>

<!-- Quick Add to Watchlist -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Negara ke Pantauan</div>
    <div class="card-body">
        <form action="{{ route('watchlist.add') }}" method="POST" class="row g-2 align-items-center">
            @csrf
            <div class="col-md-8 col-sm-9">
                <select name="country_id" class="form-select" required style="border-radius: 10px;">
                    <option value="">Pilih Negara...</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}">{{ $c->country_name }} ({{ $c->country_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-sm-3 d-grid">
                <button class="btn btn-primary" style="border-radius: 10px;"><i class="bi bi-bookmark-plus me-1"></i>Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Watchlist Cards -->
@if($watchlists->count() > 0)
<div class="row g-3">
    @foreach($watchlists as $item)
    @php
        $country  = $item->country;
        $risk     = $country?->riskScore;
        $weather  = $country?->weatherData;
        $riskLevel = $risk?->risk_level ?? 'N/A';
        $riskColor = match($riskLevel) {
            'High'   => ['border'=>'#f43f5e','badge'=>'badge-risk-high'],
            'Medium' => ['border'=>'#f59e0b','badge'=>'badge-risk-medium'],
            'Low'    => ['border'=>'#10b981','badge'=>'badge-risk-low'],
            default  => ['border'=>'#e2e8f0','badge'=>'bg-light text-secondary border'],
        };
    @endphp
    <div class="col-md-4 col-sm-6">
        <div class="watchlist-card h-100" style="border-top: 4px solid {{ $riskColor['border'] }};">
            <div class="watchlist-header">
                @if($country?->flag_url)
                <img src="{{ $country->flag_url }}" width="44" height="29"
                     style="border-radius:4px;border:1px solid #e2e8f0;object-fit:cover; flex-shrink:0;"
                     alt="{{ $country->country_name }}"
                     loading="lazy">
                @endif
                <div class="min-width-0">
                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $country?->country_name }}</div>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $country?->region }}</small>
                </div>
                @if($riskLevel !== 'N/A')
                <span class="badge {{ $riskColor['badge'] }} ms-auto">{{ $riskLevel }}</span>
                @endif
            </div>

            <div class="watchlist-body d-flex flex-column h-100">
                @if($risk)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size: 0.75rem;">Risk Score</span>
                        <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $risk->total_score }}/100</span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:10px;">
                        <div class="progress-bar {{ $riskLevel === 'High' ? 'bg-danger' : ($riskLevel === 'Medium' ? 'bg-warning' : 'bg-success') }}" style="width:{{ $risk->total_score }}%"></div>
                    </div>
                </div>
                @endif

                @if($weather)
                <div class="d-flex gap-2 flex-wrap mb-4">
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1" style="font-size:0.7rem; font-weight: 500;">
                        🌡️ {{ $weather->temperature }}°C
                    </span>
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1" style="font-size:0.7rem; font-weight: 500;">
                        💨 {{ $weather->wind_speed }} km/h
                    </span>
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1" style="font-size:0.7rem; font-weight: 500;">
                        🌧️ {{ $weather->rainfall }} mm
                    </span>
                </div>
                @endif

                <div class="d-flex gap-2 mt-auto pt-2">
                    <a href="{{ route('countries.show', $country) }}" class="btn btn-sm btn-outline-primary flex-grow-1" style="border-radius: 8px; font-size:0.75rem; font-weight:600;">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <form action="{{ route('watchlist.remove', $country) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-size:0.75rem;" onclick="return confirm('Hapus negara {{ $country?->country_name }} dari watchlist?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card shadow-sm border">
    <div class="card-body text-center py-5">
        <i class="bi bi-bookmark-star display-3 d-block mb-3 text-secondary opacity-30"></i>
        <h5 class="fw-bold">Watchlist Kosong</h5>
        <p class="text-muted small">Tambahkan beberapa negara di atas untuk mulai memantau tingkat risiko rantai pasok.</p>
    </div>
</div>
@endif

@endsection