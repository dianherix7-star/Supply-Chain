@extends('layouts.app')

@section('title', 'Watchlist Saya')
@section('page-title', 'Watchlist — Negara Favorit')

@push('styles')
<style>
    .risk-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="section-title">⭐ Watchlist Saya</h1>
        <p class="section-sub">Pantau negara favorit Anda dalam satu halaman</p>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top: 4px solid #6366f1 !important;">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1">Negara Dipantau</div>
                <div class="text-primary fw-extrabold fs-2" style="font-weight:800;">{{ $stats['total_watchlist'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top: 4px solid #f43f5e !important;">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1 d-flex align-items-center gap-2">
                    <span class="risk-indicator-dot" style="background-color: #ef4444;"></span> High Risk
                </div>
                <div class="text-danger fw-extrabold fs-2" style="font-weight:800;">{{ $stats['high_risk'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top: 4px solid #f59e0b !important;">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1 d-flex align-items-center gap-2">
                    <span class="risk-indicator-dot" style="background-color: #f59e0b;"></span> Medium Risk
                </div>
                <div class="text-warning fw-extrabold fs-2" style="font-weight:800;">{{ $stats['medium_risk'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top: 4px solid #10b981 !important;">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1 d-flex align-items-center gap-2">
                    <span class="risk-indicator-dot" style="background-color: #10b981;"></span> Low Risk
                </div>
                <div class="text-success fw-extrabold fs-2" style="font-weight:800;">{{ $stats['low_risk'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Add to Watchlist Form -->
<div class="card mb-4 border-0 shadow-sm bg-white">
    <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle text-primary"></i> Tambah Negara ke Watchlist
    </div>
    <div class="card-body">
        <form action="{{ route('watchlist.add') }}" method="POST" class="row g-2 align-items-center">
            @csrf
            <div class="col-md-5">
                <select name="country_id" class="form-select border" style="border-color: #cbd5e1 !important;" required>
                    <option value="">Pilih Negara...</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-bookmark-plus"></i> Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Watchlist Grid -->
@if($watchlists->count() > 0)
<div class="row g-3">
    @foreach($watchlists as $item)
    @php
        $country  = $item->country;
        $risk     = $country?->riskScore;
        $weather  = $country?->weatherData;
        $riskLevel = $risk?->risk_level ?? 'N/A';
        $riskColor = match($riskLevel) {
            'High'   => ['border'=>'#f43f5e','badge'=>'badge-risk-high','bg'=>'#fef2f2','text'=>'#ef4444'],
            'Medium' => ['border'=>'#f59e0b','badge'=>'badge-risk-medium','bg'=>'#fffbeb','text'=>'#d97706'],
            'Low'    => ['border'=>'#10b981','badge'=>'badge-risk-low','bg'=>'#f0fdf4','text'=>'#16a34a'],
            default  => ['border'=>'#cbd5e1','badge'=>'bg-light text-secondary border','bg'=>'#f8fafc','text'=>'#64748b'],
        };
    @endphp
    <div class="col-md-4 col-lg-3">
        <div class="card h-100 border-0 shadow-sm bg-white" style="border-top: 4px solid {{ $riskColor['border'] }} !important;">
            <div class="card-body p-3 d-flex flex-column">
                <!-- Flag + Name -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($country?->flag_url)
                    <img src="{{ $country->flag_url }}" width="48" height="32"
                         style="border-radius:4px;border:1px solid #e2e8f0;object-fit:cover;"
                         alt="{{ $country->country_name }}"
                         loading="lazy">
                    @endif
                    <div>
                        <div class="fw-bold text-dark">{{ $country?->country_name }}</div>
                        <small class="text-muted">{{ $country?->region }}</small>
                    </div>
                </div>

                <!-- Risk Badge -->
                @if($risk)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Risk Score</span>
                    <div>
                        <span class="fw-bold text-dark">{{ $risk->total_score }}/100</span>
                        <span class="badge {{ $riskColor['badge'] }} ms-1 text-uppercase">{{ $riskLevel }}</span>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="progress mb-3" style="height:6px; border-radius:10px;">
                    @php $pct = $risk->total_score; @endphp
                    <div class="progress-bar {{ $riskColor['badge'] === 'badge-risk-high' ? 'bg-danger' : ($riskColor['badge'] === 'badge-risk-medium' ? 'bg-warning' : 'bg-success') }}" style="width:{{ $pct }}%"></div>
                </div>
                @else
                <div class="p-2 bg-light text-muted rounded-3 text-center small mb-3" style="font-size:0.75rem;">Risk score belum dihitung</div>
                @endif

                <!-- Weather -->
                @if($weather)
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span class="badge bg-light text-dark border">
                        🌡️ {{ $weather->temperature }}°C
                    </span>
                    <span class="badge bg-light text-dark border">
                        💨 {{ $weather->wind_speed }} km/h
                    </span>
                    @if($weather->storm_risk >= 3)
                    <span class="badge bg-danger text-white">⚡ Badai</span>
                    @endif
                </div>
                @endif

                <!-- Currency -->
                @if($country?->currency)
                <div class="text-muted small mb-3 mt-auto">
                    <i class="bi bi-currency-exchange me-1 text-primary"></i>{{ $country->currency }}
                </div>
                @endif

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <a href="{{ route('countries.show', $country) }}" class="btn btn-sm btn-outline-primary flex-grow-1" style="border-radius: 8px;">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <form action="{{ route('watchlist.remove', $country) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('Hapus dari watchlist?')" title="Hapus">
                            <i class="bi bi-bookmark-dash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card border-0 shadow-sm bg-white">
    <div class="card-body text-center py-5">
        <i class="bi bi-bookmark-star display-2 d-block mb-3 opacity-25 text-primary"></i>
        <h5 class="text-dark fw-bold">Watchlist Kosong</h5>
        <p class="text-muted small">Tambahkan negara yang ingin Anda pantau menggunakan form di atas.</p>
        <a href="{{ route('countries.index') }}" class="btn btn-outline-primary mt-2" style="border-radius: 8px;">
            <i class="bi bi-globe me-1"></i> Lihat Semua Negara
        </a>
    </div>
</div>
@endif

@endsection
