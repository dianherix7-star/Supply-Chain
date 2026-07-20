@extends('layouts.app')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')

@section('content')

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="section-title">📊 Analytics Dashboard</h1>
        <p class="section-sub">Visualisasi lengkap data ekonomi, risiko, berita, dan mata uang global</p>
    </div>
</div>

<!-- Global Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-primary fw-bold fs-1">{{ $globalStats['total_countries'] }}</div>
                <div class="text-muted small">Negara</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-info fw-bold fs-1">{{ $globalStats['total_ports'] }}</div>
                <div class="text-muted small">Pelabuhan</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-warning fw-bold fs-1">{{ $globalStats['total_news'] }}</div>
                <div class="text-muted small">Berita</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-danger fw-bold fs-1">{{ $globalStats['total_risk'] }}</div>
                <div class="text-muted small">Risk Scored</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-success fw-bold fs-1">{{ $globalStats['avg_risk_score'] }}</div>
                <div class="text-muted small">Avg Risk/100</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-1" style="color:#8b5cf6;">{{ $globalStats['total_economic'] }}</div>
                <div class="text-muted small">Economic Data</div>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: Top Risk + Sentiment -->
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-2 text-danger"></i>Top 10 Negara Risiko Tertinggi</div>
            <div class="card-body">
                @if($topRisk->count() > 0)
                <canvas id="topRiskChart" height="220"></canvas>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calculator opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data risk score. Hitung dari menu <strong>Risk Score</strong>.
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-chat-square-text me-2 text-success"></i>Sentiment Analysis</div>
            <div class="card-body">
                @if($sentimentStats['total'] > 0)
                <canvas id="sentimentDonut" height="200"></canvas>
                <div class="row g-2 mt-2 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded" style="background: rgba(34, 197, 94, 0.1);">
                            <div class="fw-bold text-success">{{ $sentimentStats['pos_pct'] }}%</div>
                            <div class="text-muted" style="font-size:0.7rem;">Positive</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded" style="background: rgba(239, 68, 68, 0.1);">
                            <div class="fw-bold text-danger">{{ $sentimentStats['neg_pct'] }}%</div>
                            <div class="text-muted" style="font-size:0.7rem;">Negative</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded" style="background: rgba(255, 255, 255, 0.05);">
                            <div class="fw-bold text-secondary">{{ $sentimentStats['neu_pct'] }}%</div>
                            <div class="text-muted" style="font-size:0.7rem;">Neutral</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-newspaper opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data berita.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 2: GDP + Inflation -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-graph-up me-2 text-primary"></i>Top 10 GDP Terbesar</div>
            <div class="card-body">
                @if($gdpData->count() > 0)
                <canvas id="gdpChart" height="240"></canvas>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-graph-up opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data ekonomi. Fetch dari menu <strong>Economic Data</strong>.
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-arrow-up-right me-2 text-warning"></i>Top 10 Inflasi Tertinggi</div>
            <div class="card-body">
                @if($inflationData->count() > 0)
                <canvas id="inflationChart" height="240"></canvas>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-graph-up opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data inflasi.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Currency + Risk by Region -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-currency-exchange me-2 text-info"></i>10 Mata Uang Paling Lemah vs USD</div>
            <div class="card-body">
                @if($weakCurrencies->count() > 0)
                <canvas id="currencyChart" height="240"></canvas>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-currency-exchange opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data kurs. Fetch dari menu <strong>Currency</strong>.
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-globe me-2 text-purple"></i>Risk Score per Region</div>
            <div class="card-body">
                @if($riskByRegion->count() > 0)
                <canvas id="regionRiskChart" height="240"></canvas>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-globe opacity-25 display-3 d-block mb-3"></i>
                    Belum ada data.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- High Risk Weather -->
@if($highRiskWeather->count() > 0)
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-cloud-lightning-rain me-2 text-warning"></i>Negara dengan Risiko Cuaca Tinggi</div>
    <div class="card-body">
        <div class="row g-2">
            @foreach($highRiskWeather as $w)
            <div class="col-md-3 col-6">
                <div class="p-2 border rounded d-flex align-items-center gap-2">
                    @if($w->country?->flag_url)
                    <img src="{{ $w->country->flag_url }}" width="28" height="19" style="border-radius:2px;object-fit:cover;"
                         alt="{{ $w->country->country_name }}" loading="lazy">
                    @endif
                    <div>
                        <div class="small fw-semibold">{{ $w->country?->country_name }}</div>
                        <div class="d-flex gap-1">
                            @for($i = 0; $i < $w->storm_risk; $i++)
                            <span style="color:#f59e0b;font-size:10px;">⚡</span>
                            @endfor
                            <span class="text-muted" style="font-size:0.7rem;">Storm {{ $w->storm_risk }}/5</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Configure Global Chart defaults for Dark Mode
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#94a3b8'; // Light slate text for dark mode
}

// Top Risk Chart
@if($topRisk->count() > 0)
new Chart(document.getElementById('topRiskChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($topRisk->pluck('country.country_name')),
        datasets: [{
            label: 'Risk Score',
            data: @json($topRisk->pluck('total_score')),
            backgroundColor: @json($topRisk->map(fn($r) => $r->risk_level === 'High' ? '#ef4444' : ($r->risk_level === 'Medium' ? '#f59e0b' : '#22c55e'))->values()),
            borderRadius: 4,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { grid: { display: false }, ticks: { font: { size: 12 } } }
        }
    }
});
@endif

// Sentiment Doughnut
@if($sentimentStats['total'] > 0)
new Chart(document.getElementById('sentimentDonut').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Positive', 'Negative', 'Neutral'],
        datasets: [{
            data: [{{ $sentimentStats['positive'] }}, {{ $sentimentStats['negative'] }}, {{ $sentimentStats['neutral'] }}],
            backgroundColor: ['#22c55e', '#ef4444', '#94a3b8'],
            borderWidth: 3, borderColor: '#fff', hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 12 } } }
    }
});
@endif

// GDP Chart
@if($gdpData->count() > 0)
new Chart(document.getElementById('gdpChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($gdpData->pluck('country.country_name')),
        datasets: [{
            label: 'GDP (Miliar USD)',
            data: @json($gdpData->map(fn($e) => $e->gdp ? round($e->gdp / 1e9, 1) : 0)->values()),
            backgroundColor: 'rgba(59,130,246,0.7)',
            borderColor: '#3b82f6',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` GDP: $${ctx.raw}B` } }
        },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
    }
});
@endif

// Inflation Chart
@if($inflationData->count() > 0)
new Chart(document.getElementById('inflationChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($inflationData->pluck('country.country_name')),
        datasets: [{
            label: 'Inflasi (%)',
            data: @json($inflationData->pluck('inflation')),
            backgroundColor: 'rgba(245,158,11,0.7)',
            borderColor: '#f59e0b',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Inflasi: ${ctx.raw}%` } }
        },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
    }
});
@endif

// Currency Chart
@if($weakCurrencies->count() > 0)
new Chart(document.getElementById('currencyChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($weakCurrencies->pluck('currency')),
        datasets: [{
            label: 'Rate vs USD',
            data: @json($weakCurrencies->pluck('exchange_rate')),
            backgroundColor: 'rgba(6,182,212,0.7)',
            borderColor: '#06b6d4',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` 1 USD = ${ctx.raw} ${ctx.label}` } }
        },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } }
    }
});
@endif

// Region Risk Chart
@if($riskByRegion->count() > 0)
new Chart(document.getElementById('regionRiskChart').getContext('2d'), {
    type: 'radar',
    data: {
        labels: @json($riskByRegion->keys()),
        datasets: [{
            label: 'Avg Risk Score',
            data: @json($riskByRegion->values()),
            backgroundColor: 'rgba(139,92,246,0.2)',
            borderColor: '#8b5cf6',
            pointBackgroundColor: '#8b5cf6',
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        scales: {
            r: { 
                beginAtZero: true, 
                max: 100, 
                ticks: { font: { size: 10 }, backdropColor: 'transparent', color: '#94a3b8' },
                grid: { color: 'rgba(255,255,255,0.1)' },
                angleLines: { color: 'rgba(255,255,255,0.1)' }
            }
        },
        plugins: { legend: { display: false } }
    }
});
@endif
</script>
@endpush
