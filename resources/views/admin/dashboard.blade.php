@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@push('styles')
<style>
    /* Elegant SaaS style borders for Stat Cards */
    .stat-card-blue   { border-top: 4px solid #6366f1; }
    .stat-card-teal   { border-top: 4px solid #0d9488; }
    .stat-card-purple { border-top: 4px solid #8b5cf6; }
    .stat-card-orange { border-top: 4px solid #f97316; }
    .stat-card-red    { border-top: 4px solid #f43f5e; }
    .stat-card-green  { border-top: 4px solid #10b981; }

    /* Risk indicators */
    .risk-indicator-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: #ffffff;
        transition: var(--transition);
        position: relative;
    }
    .risk-indicator-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-top-left-radius: var(--radius-md);
        border-bottom-left-radius: var(--radius-md);
    }
    .risk-high::before   { background-color: #f43f5e; }
    .risk-medium::before { background-color: #f59e0b; }
    .risk-low::before    { background-color: #10b981; }
    .risk-avg::before    { background-color: #6366f1; }

    /* Action Buttons Revamp */
    .btn-action-soft {
        background: #ffffff;
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 0.825rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-soft:hover {
        background: var(--background);
        border-color: #cbd5e1;
    }
    .btn-action-soft-success { background: rgba(16, 185, 129, 0.06); color: #059669; border: 1px solid rgba(16, 185, 129, 0.12); }
    .btn-action-soft-success:hover { background: rgba(16, 185, 129, 0.12); }
    
    .btn-action-soft-info { background: rgba(6, 182, 212, 0.06); color: #0891b2; border: 1px solid rgba(6, 182, 212, 0.12); }
    .btn-action-soft-info:hover { background: rgba(6, 182, 212, 0.12); }
    
    .btn-action-soft-warning { background: rgba(245, 158, 11, 0.06); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.12); }
    .btn-action-soft-warning:hover { background: rgba(245, 158, 11, 0.12); }
    
    .btn-action-soft-danger { background: rgba(244, 63, 94, 0.06); color: #e11d48; border: 1px solid rgba(244, 63, 94, 0.12); }
    .btn-action-soft-danger:hover { background: rgba(244, 63, 94, 0.12); }

    /* Custom news feed */
    .feed-item {
        transition: var(--transition);
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
    }
    .feed-item:hover {
        background-color: #f8fafc;
    }
</style>
@endpush

@section('content')

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="section-title">🌐 Supply Chain Intelligence</h1>
        <p class="section-sub">Overview seluruh data dan analisis risiko rantai pasok global</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('countries.import') }}" class="btn btn-action-soft">
            <i class="bi bi-cloud-download text-primary"></i> Import Countries
        </a>
        <a href="{{ route('analytics.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-bar-chart-line"></i> Analytics
        </a>
    </div>
</div>

<!-- Stats Row 1: Main KPIs -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-blue">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Total Negara</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_countries'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-globe me-1 text-primary"></i>Countries</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-teal">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Pelabuhan</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_ports'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-anchor me-1 text-teal" style="color: #0d9488;"></i>Ports</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-purple">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Data Cuaca</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_weather'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-cloud-sun me-1 text-purple" style="color: #8b5cf6;"></i>Weather</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-orange">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Total Berita</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_news'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-newspaper me-1 text-orange" style="color: #f97316;"></i>News</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-red">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Risk Scores</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_risk'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-exclamation-triangle me-1 text-danger"></i>Risk</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-green">
            <div class="text-muted fw-semibold" style="font-size:0.75rem;">Data Ekonomi</div>
            <div class="text-dark fw-bold" style="font-size:1.8rem;line-height:1.2;margin:6px 0 4px;">{{ $stats['total_economic'] }}</div>
            <div class="text-secondary" style="font-size:0.75rem;"><i class="bi bi-graph-up me-1 text-success"></i>Economic</div>
        </div>
    </div>
</div>

<!-- Risk Level & Avg Score -->
@if($stats['total_risk'] > 0)
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="risk-indicator-card risk-high p-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-block rounded-circle" style="width: 8px; height: 8px; background-color: #ef4444;"></span>
                <span class="fw-semibold text-muted small">High Risk</span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-2 fw-extrabold text-danger" style="font-weight:800;">{{ $stats['high_risk'] }}</span>
                <span class="text-muted small">negara</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="risk-indicator-card risk-medium p-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-block rounded-circle" style="width: 8px; height: 8px; background-color: #f59e0b;"></span>
                <span class="fw-semibold text-muted small">Medium Risk</span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-2 fw-extrabold text-warning" style="font-weight:800;">{{ $stats['medium_risk'] }}</span>
                <span class="text-muted small">negara</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="risk-indicator-card risk-low p-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-block rounded-circle" style="width: 8px; height: 8px; background-color: #10b981;"></span>
                <span class="fw-semibold text-muted small">Low Risk</span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-2 fw-extrabold text-success" style="font-weight:800;">{{ $stats['low_risk'] }}</span>
                <span class="text-muted small">negara</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="risk-indicator-card risk-avg p-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-block rounded-circle" style="width: 8px; height: 8px; background-color: #6366f1;"></span>
                <span class="fw-semibold text-muted small">Avg Score</span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-2 fw-extrabold text-primary" style="font-weight:800;">{{ $stats['avg_risk_score'] }}</span>
                <span class="text-muted small">/ 100</span>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Top 5 Risk Countries Chart -->
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2 text-danger"></i>Top Negara Risiko Tertinggi</span>
                <a href="{{ route('risk.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; font-size: 0.75rem;">Lihat semua</a>
            </div>
            <div class="card-body">
                @if($topRiskCountries->count() > 0)
                <canvas id="topRiskChart" height="200"></canvas>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bar-chart opacity-25 display-4 d-block mb-2"></i>
                    Belum ada data risk score. Hitung dari menu <strong>Risk Score</strong>.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Risk Distribution Doughnut -->
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Distribusi Level Risiko
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($stats['total_risk'] > 0)
                <canvas id="riskDonutChart" width="220" height="220"></canvas>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-pie-chart opacity-25 display-4 d-block mb-2"></i>
                    Belum ada data.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- News Sentiment & Recent News -->
<div class="row g-3 mb-4">
    <!-- Sentiment Summary -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-chat-square-text me-2 text-success"></i>Sentimen Berita</div>
            <div class="card-body">
                @if($stats['total_news'] > 0)
                @php
                    $total = $stats['total_news'];
                    $posPct = round(($stats['positive_news'] / $total) * 100);
                    $negPct = round(($stats['negative_news'] / $total) * 100);
                    $neuPct = 100 - $posPct - $negPct;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-success fw-semibold">Positive</span>
                        <span class="small">{{ $posPct }}%</span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:10px;">
                        <div class="progress-bar bg-success" style="width:{{ $posPct }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-danger fw-semibold">Negative</span>
                        <span class="small">{{ $negPct }}%</span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:10px;">
                        <div class="progress-bar bg-danger" style="width:{{ $negPct }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary fw-semibold">Neutral</span>
                        <span class="small">{{ $neuPct }}%</span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:10px;">
                        <div class="progress-bar bg-secondary" style="width:{{ $neuPct }}%"></div>
                    </div>
                </div>
                <div class="text-center mt-3 d-flex justify-content-center">
                    <canvas id="sentimentChart" width="150" height="150"></canvas>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-newspaper opacity-25 display-4 d-block mb-2"></i>
                    Belum ada data berita.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent News -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-newspaper me-2"></i>Berita Risiko Terbaru</span>
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; font-size: 0.75rem;">Lihat semua</a>
            </div>
            <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                @forelse($recentNews as $item)
                <div class="feed-item d-flex align-items-start gap-3">
                    @if($item->country?->flag_url)
                    <img src="{{ $item->country->flag_url }}" width="28" height="19" class="mt-1" style="border-radius:3px; object-fit:cover; border:1px solid #e2e8f0; flex-shrink:0;"
                         alt="{{ $item->country->country_name }}" loading="lazy">
                    @else
                    <div style="width:28px; height:19px; background:#e2e8f0; border-radius:3px;" class="mt-1"></div>
                    @endif
                    <div class="flex-grow-1 min-width-0">
                        <div class="small fw-semibold text-dark text-truncate-2" style="font-size:0.88rem; line-height: 1.35;">
                            {{ $item->title }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge {{ $item->sentiment === 'Positive' ? 'badge-risk-low' : ($item->sentiment === 'Negative' ? 'badge-risk-high' : 'bg-light text-secondary border') }}">
                                {{ $item->sentiment ?? 'Neutral' }}
                            </span>
                            <span class="text-muted" style="font-size:0.7rem;">
                                {{ $item->country?->country_name }} •
                                {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : '' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-newspaper opacity-25 display-3 d-block mb-2"></i>
                    Belum ada berita. Ambil data berita dari menu <strong>News</strong>.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat (Quick Actions)</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('countries.import') }}" class="btn btn-action-soft btn-action-soft-success">
                <i class="bi bi-cloud-download"></i> Import Countries
            </a>
            <form action="{{ route('weather.fetch-all') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-action-soft btn-action-soft-info"><i class="bi bi-cloud-sun"></i> Fetch Weather</button>
            </form>
            <form action="{{ route('currency.fetch-all') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-action-soft btn-action-soft-warning"><i class="bi bi-currency-exchange"></i> Fetch Currency</button>
            </form>
            <form action="{{ route('risk.calculate-all') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-action-soft btn-action-soft-danger"><i class="bi bi-calculator"></i> Hitung Ulang Risiko</button>
            </form>
            <a href="{{ route('analytics.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-1" style="font-size:0.825rem; padding:8px 16px; border-radius:10px;">
                <i class="bi bi-bar-chart-line"></i> Analytics Dashboard
            </a>
            <a href="{{ route('compare.index') }}" class="btn btn-action-soft">
                <i class="bi bi-arrow-left-right text-secondary"></i> Bandingkan Negara
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@php
    $topRiskColors = $topRiskCountries->map(function($r) {
        if ($r->risk_level === 'High') {
            return 'rgba(244, 63, 94, 0.85)';
        } elseif ($r->risk_level === 'Medium') {
            return 'rgba(245, 158, 11, 0.85)';
        }
        return 'rgba(16, 185, 129, 0.85)';
    });
@endphp
<script>
// Configure Global Chart defaults
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';
}

// Top Risk Bar Chart
@if($topRiskCountries->count() > 0)
const topRiskCtx = document.getElementById('topRiskChart').getContext('2d');
new Chart(topRiskCtx, {
    type: 'bar',
    data: {
        labels: @json($topRiskCountries->pluck('country.country_name')),
        datasets: [{
            label: 'Risk Score',
            data: @json($topRiskCountries->pluck('total_score')),
            backgroundColor: @json($topRiskColors),
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { 
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8,
                callbacks: { label: ctx => ` Risk Score: ${ctx.raw}/100` } 
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                max: 100, 
                grid: { color: 'rgba(226, 232, 240, 0.6)' },
                ticks: { padding: 8 }
            },
            x: { 
                grid: { display: false },
                ticks: { padding: 8 }
            }
        }
    }
});
@endif

// Risk Distribution Doughnut
@if($stats['total_risk'] > 0)
const donutCtx = document.getElementById('riskDonutChart').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ['High Risk', 'Medium Risk', 'Low Risk'],
        datasets: [{
            data: [{{ $stats['high_risk'] }}, {{ $stats['medium_risk'] }}, {{ $stats['low_risk'] }}],
            backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: false,
        cutout: '75%',
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { 
                    padding: 16, 
                    boxWidth: 10,
                    boxHeight: 10,
                    font: { size: 11, weight: '600' } 
                } 
            },
            tooltip: {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8
            }
        }
    }
});
@endif

// Sentiment Doughnut
@if($stats['total_news'] > 0)
const sentCtx = document.getElementById('sentimentChart').getContext('2d');
new Chart(sentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Positive', 'Negative', 'Neutral'],
        datasets: [{
            data: [{{ $stats['positive_news'] }}, {{ $stats['negative_news'] }}, {{ max(0, $stats['total_news'] - $stats['positive_news'] - $stats['negative_news']) }}],
            backgroundColor: ['#10b981', '#f43f5e', '#64748b'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: false,
        cutout: '75%',
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8
            }
        }
    }
});
@endif
</script>
@endpush