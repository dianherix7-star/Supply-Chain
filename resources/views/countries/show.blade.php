@extends('layouts.app')

@section('page-title', $country->country_name . ' — Detail')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #country-map { 
        height: 320px; 
        border-radius: 10px; 
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    @if($country->flag_url)
        <img src="{{ $country->flag_url }}" width="72" height="48"
             style="border:1px solid #e2e8f0;border-radius:6px;object-fit:cover;"
             alt="{{ $country->country_name }}"
             loading="lazy">
    @endif
    <div>
        <h4 class="fw-bold mb-0 text-dark">{{ $country->country_name }}</h4>
        <span class="text-muted small">{{ $country->capital ?? '' }}
            @if($country->region) · {{ $country->region }} @endif
            @if($country->subregion) / {{ $country->subregion }} @endif
        </span>
    </div>
    <div class="ms-auto d-flex gap-2">
        {{-- Watchlist Button --}}
        @if($inWatchlist)
            <form action="{{ route('watchlist.remove', $country->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" style="border-radius: 8px;">
                    <i class="bi bi-heart-fill"></i> Hapus Watchlist
                </button>
            </form>
        @else
            <form action="{{ route('watchlist.add') }}" method="POST">
                @csrf
                <input type="hidden" name="country_id" value="{{ $country->id }}">
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" style="border-radius: 8px;">
                    <i class="bi bi-heart"></i> + Watchlist
                </button>
            </form>
        @endif
        <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ROW 1: Info cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card p-3" style="border-top: 4px solid #6366f1;">
            <div class="fw-semibold text-muted small mb-1">Kode Negara</div>
            <div class="fs-4 fw-bold text-dark">{{ $country->country_code }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-3" style="border-top: 4px solid #10b981;">
            <div class="fw-semibold text-muted small mb-1">Populasi</div>
            <div class="fs-4 fw-bold text-dark">
                {{ $country->population ? number_format($country->population / 1000000, 1) . 'M' : '—' }}
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-3" style="border-top: 4px solid #f59e0b;">
            <div class="fw-semibold text-muted small mb-1">Mata Uang</div>
            <div class="fs-4 fw-bold text-dark">{{ $country->currency ?? '—' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3" style="border-top: 4px solid #06b6d4;">
            <div class="fw-semibold text-muted small mb-1">GDP (Terakhir)</div>
            <div class="fs-4 fw-bold text-dark">
                @if($latestEco && $latestEco->gdp)
                    ${{ number_format($latestEco->gdp / 1e9, 1) }}B
                @else — @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @php
            $riskColor = $riskScore ? ($riskScore->risk_level == 'High' ? '#f43f5e' : ($riskScore->risk_level == 'Medium' ? '#f59e0b' : '#10b981')) : '#94a3b8';
            $riskBg = $riskScore ? ($riskScore->risk_level == 'High' ? '#fef2f2' : ($riskScore->risk_level == 'Medium' ? '#fffbeb' : '#f0fdf4')) : '#f8fafc';
            $riskText = $riskScore ? ($riskScore->risk_level == 'High' ? '#ef4444' : ($riskScore->risk_level == 'Medium' ? '#d97706' : '#16a34a')) : '#64748b';
        @endphp
        <div class="card p-3" style="border-top: 4px solid {{ $riskColor }};">
            <div class="fw-semibold text-muted small mb-1">Risk Score</div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="fs-4 fw-bold text-dark">{{ $riskScore ? $riskScore->total_score . '/100' : '—' }}</div>
                @if($riskScore)
                    <span class="badge text-uppercase" style="background-color: {{ $riskBg }}; color: {{ $riskText }}; border: 1px solid rgba(0,0,0,0.05);">
                        {{ $riskScore->risk_level }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ROW 2: Weather + Map --}}
<div class="row g-3 mb-4">
    {{-- Cuaca --}}
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm bg-white">
            <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
                <i class="bi bi-cloud-sun text-primary"></i> Cuaca Saat Ini
            </div>
            <div class="card-body">
                @if($weather)
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3" style="background-color: #f8fafc !important;">
                                <div class="fs-3 fw-bold text-danger">{{ number_format($weather->temperature, 1) }}°C</div>
                                <small class="text-muted fw-medium d-block mt-1">Suhu</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3" style="background-color: #f8fafc !important;">
                                <div class="fs-3 fw-bold text-info">{{ number_format($weather->wind_speed, 0) }}</div>
                                <small class="text-muted fw-medium d-block mt-1">km/h Angin</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3" style="background-color: #f8fafc !important;">
                                <div class="fs-4 fw-bold text-primary">{{ number_format($weather->rainfall, 1) }} mm</div>
                                <small class="text-muted fw-medium d-block mt-1">Curah Hujan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 d-flex flex-column align-items-center justify-content-center" style="background-color: #f8fafc !important; min-height: 84px;">
                                @php
                                    $riskLabels = ['Aman','Kabut','Hujan','Deras','Badai','Badai Besar'];
                                    $riskColors = ['success','secondary','info','warning','danger','danger'];
                                    $sr = $weather->storm_risk ?? 0;
                                @endphp
                                <span class="badge bg-{{ $riskColors[$sr] }} text-white px-3 py-2 fs-7" style="border-radius: 20px;">{{ $riskLabels[$sr] }}</span>
                                <small class="d-block text-muted mt-2 fw-medium">Storm Risk</small>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3 text-center" style="font-size: 0.75rem;">
                        <i class="bi bi-clock me-1"></i>
                        Diperbarui {{ $weather->recorded_at ? \Carbon\Carbon::parse($weather->recorded_at)->diffForHumans() : '—' }}
                    </small>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-cloud-slash display-5 opacity-25"></i>
                        <p class="mt-2 small">Belum ada data cuaca.<br>Fetch dari halaman Weather.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Peta Lokasi --}}
    <div class="col-md-8">
        <div class="card h-100 border-0 shadow-sm bg-white">
            <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt text-danger"></i> Lokasi Negara
            </div>
            <div class="card-body p-3">
                @if($country->latitude && $country->longitude)
                    <div id="country-map"></div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-geo display-5 opacity-25"></i>
                        <p class="mt-2">Koordinat tidak tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ROW 3: GDP Chart + Risk Breakdown --}}
<div class="row g-3 mb-4">
    {{-- GDP Trend --}}
    <div class="col-md-7">
        <div class="card h-100 border-0 shadow-sm bg-white">
            <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-success"></i> Tren GDP & Inflasi
            </div>
            <div class="card-body p-3">
                @if($economicHistory->count() > 0)
                    <canvas id="gdpChart" height="120"></canvas>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-graph-up display-5 opacity-25"></i>
                        <p class="mt-2 small">Belum ada data ekonomi.<br>Fetch dari halaman Economic Data.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Risk Breakdown --}}
    <div class="col-md-5">
        <div class="card h-100 border-0 shadow-sm bg-white">
            <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
                <i class="bi bi-shield-exclamation text-warning"></i> Breakdown Risk Score
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center">
                @if($riskScore)
                    <canvas id="riskChart" height="180"></canvas>
                @else
                    <div class="text-center text-muted py-5 w-100">
                        <i class="bi bi-calculator display-5 opacity-25"></i>
                        <p class="mt-2 small">Belum ada risk score.<br>Hitung dari halaman Risk Score.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ROW 4: News --}}
<div class="card mb-4 border-0 shadow-sm bg-white">
    <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
        <i class="bi bi-newspaper text-secondary"></i> Berita Terkini — {{ $country->country_name }}
    </div>
    <div class="card-body p-3">
        @if($latestNews->count() > 0)
            <div class="row g-3">
            @foreach($latestNews as $item)
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light" style="background-color: #f8fafc !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-semibold" style="font-size: 0.75rem;">{{ $item->source ?? 'News Source' }}</small>
                            @php $sc = match($item->sentiment) { 'Positive' => 'badge-risk-low', 'Negative' => 'badge-risk-high', default => 'bg-light text-secondary border' }; @endphp
                            <span class="badge {{ $sc }}">{{ $item->sentiment ?? 'Neutral' }}</span>
                        </div>
                        <p class="mb-2 small fw-semibold" style="line-height: 1.4;">
                            @if($item->url)
                                <a href="{{ $item->url }}" target="_blank" class="text-dark text-decoration-none country-link">{{ Str::limit($item->title, 90) }}</a>
                            @else
                                {{ Str::limit($item->title, 90) }}
                            @endif
                        </p>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : '' }}</small>
                    </div>
                </div>
            @endforeach
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-newspaper opacity-25 fs-3"></i>
                <p class="small mt-1 mb-0">Belum ada berita untuk negara ini.</p>
            </div>
        @endif
    </div>
</div>

{{-- Ports di negara ini --}}
@if($ports->count() > 0)
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2">
        <i class="bi bi-anchor text-primary"></i> Pelabuhan di {{ $country->country_name }}
    </div>
    <div class="card-body p-3">
        <div class="row g-2">
            @foreach($ports as $port)
            <div class="col-md-4">
                <div class="p-2 border rounded-3 d-flex align-items-center gap-2 bg-light" style="background-color: #f8fafc !important;">
                    <i class="bi bi-anchor text-primary"></i>
                    <span class="small fw-semibold text-dark">{{ $port->port_name }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Configure Global Chart defaults
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';
}

// Peta negara
@if($country->latitude && $country->longitude)
const countryMap = L.map('country-map').setView([{{ $country->latitude }}, {{ $country->longitude }}], 4);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(countryMap);

// Custom country pin - Beautiful red circle pin
const countryIcon = L.divIcon({
    html: `
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger border border-2 border-white text-white shadow-sm" 
             style="width: 28px; height: 28px;">
            <i class="bi bi-geo-alt-fill" style="font-size: 14px;"></i>
        </div>
    `,
    iconSize: [28, 28],
    iconAnchor: [14, 14],
    className: 'custom-div-icon'
});

L.marker([{{ $country->latitude }}, {{ $country->longitude }}], { icon: countryIcon })
    .addTo(countryMap)
    .bindPopup('<strong>{{ $country->country_name }}</strong><br>{{ $country->capital ?? "" }}')
    .openPopup();
@endif

// GDP Chart
@if($economicHistory->count() > 0)
const gdpCtx = document.getElementById('gdpChart').getContext('2d');
new Chart(gdpCtx, {
    type: 'bar',
    data: {
        labels: @json($economicHistory->pluck('year')),
        datasets: [
            {
                label: 'GDP (Miliar USD)',
                data: @json($economicHistory->map(fn($e) => $e->gdp ? round($e->gdp / 1e9, 2) : null)),
                backgroundColor: 'rgba(99, 102, 241, 0.75)',
                borderColor: '#4f46e5',
                borderWidth: 0,
                borderRadius: 4,
                yAxisID: 'y',
            },
            {
                label: 'Inflasi (%)',
                data: @json($economicHistory->pluck('inflation')),
                type: 'line',
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                yAxisID: 'y1',
                tension: 0.3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { 
                position: 'top',
                labels: { font: { weight: '600', size: 11 } }
            },
            tooltip: {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: { 
                position: 'left', 
                title: { display: true, text: 'GDP (Miliar USD)', font: { weight: '600' } },
                grid: { color: 'rgba(226, 232, 240, 0.6)' }
            },
            y1: { 
                position: 'right', 
                title: { display: true, text: 'Inflasi (%)', font: { weight: '600' } }, 
                grid: { drawOnChartArea: false } 
            }
        }
    }
});
@endif

// Risk Breakdown Doughnut Chart
@if($riskScore)
const riskCtx = document.getElementById('riskChart').getContext('2d');
new Chart(riskCtx, {
    type: 'doughnut',
    data: {
        labels: ['Weather', 'Inflation', 'Currency', 'News'],
        datasets: [{
            data: [
                {{ $riskScore->weather_score }},
                {{ $riskScore->inflation_score }},
                {{ $riskScore->currency_score }},
                {{ $riskScore->news_score }},
            ],
            backgroundColor: ['#06b6d4', '#f59e0b', '#8b5cf6', '#f43f5e'],
            borderWidth: 3,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '75%',
        plugins: {
            legend: { 
                position: 'bottom',
                labels: { padding: 16, boxWidth: 10, boxHeight: 10, font: { weight: '600', size: 11 } }
            },
            tooltip: {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw}/25`
                }
            }
        }
    }
});
@endif
</script>
@endpush
