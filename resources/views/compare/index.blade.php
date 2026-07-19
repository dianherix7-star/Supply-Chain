@extends('layouts.app')

@section('title', 'Country Comparison')
@section('page-title', 'Country Comparison Engine')

@push('styles')
<style>
.compare-card {
    border-radius: 16px;
    overflow: hidden;
}

.country-header {
    padding: 20px;
    color: white;
    text-align: center;
}

.country-a-header { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.country-b-header { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

.compare-row {
    display: flex;
    align-items: stretch;
    border-bottom: 1px solid #f1f5f9;
}

.compare-row:last-child { border-bottom: none; }

.compare-label {
    width: 160px;
    min-width: 160px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-left: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
}

.compare-value {
    flex: 1;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 0.9rem;
}

.compare-value.winner {
    background: rgba(34,197,94,0.05);
    font-weight: 700;
}

.compare-value.loser {
    background: rgba(239,68,68,0.03);
    color: #94a3b8;
}
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="section-title">🔄 Country Comparison Engine</h1>
        <p class="section-sub">Bandingkan 2 negara berdasarkan GDP, Inflasi, Risk Score, Cuaca, dan Kurs</p>
    </div>
</div>

<!-- Form Pilih Negara -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-sliders me-2"></i>Pilih Negara yang Dibandingkan</div>
    <div class="card-body">
        <form action="{{ route('compare.result') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-primary">🔵 Negara A</label>
                <select name="country_a" class="form-select" required>
                    <option value="">Pilih Negara A...</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}"
                        {{ isset($countryA) && $countryA['country']->id == $c->id ? 'selected' : '' }}>
                        {{ $c->country_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-center pb-1">
                <span class="fw-bold text-muted fs-4">VS</span>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-purple" style="color:#8b5cf6;">🟣 Negara B</label>
                <select name="country_b" class="form-select" required>
                    <option value="">Pilih Negara B...</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}"
                        {{ isset($countryB) && $countryB['country']->id == $c->id ? 'selected' : '' }}>
                        {{ $c->country_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-left-right"></i> Compare
                </button>
            </div>
        </form>
    </div>
</div>

@if(isset($countryA) && isset($countryB))

{{-- Headers Countries --}}
<div class="row g-3 mb-3">
    <div class="col-md-5">
        <div class="card compare-card">
            <div class="country-header country-a-header">
                @if($countryA['country']->flag_url)
                <img src="{{ $countryA['country']->flag_url }}" width="60" height="40"
                     style="border-radius:4px;border:2px solid rgba(255,255,255,0.4);object-fit:cover;" class="mb-2"
                     alt="{{ $countryA['country']->country_name }}"
                     loading="lazy">
                @endif
                <h5 class="mb-0 fw-bold">{{ $countryA['country']->country_name }}</h5>
                <small class="opacity-75">{{ $countryA['country']->region }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="fw-bold text-muted" style="font-size:2rem;">VS</div>
            <div class="text-muted small">Comparison</div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card compare-card">
            <div class="country-header country-b-header">
                @if($countryB['country']->flag_url)
                <img src="{{ $countryB['country']->flag_url }}" width="60" height="40"
                     style="border-radius:4px;border:2px solid rgba(255,255,255,0.4);object-fit:cover;" class="mb-2"
                     alt="{{ $countryB['country']->country_name }}"
                     loading="lazy">
                @endif
                <h5 class="mb-0 fw-bold">{{ $countryB['country']->country_name }}</h5>
                <small class="opacity-75">{{ $countryB['country']->region }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Comparison Table --}}
<div class="card mb-4">
    <div class="card-header fw-semibold"><i class="bi bi-table me-2"></i>Perbandingan Detail</div>
    <div class="card-body p-0">

        {{-- Risk Score --}}
        @php
            $aRisk = $countryA['risk']?->total_score ?? 0;
            $bRisk = $countryB['risk']?->total_score ?? 0;
        @endphp
        <div class="compare-row">
            <div class="compare-value {{ $aRisk <= $bRisk ? 'winner' : 'loser' }}">
                @if($countryA['risk'])
                @php $rl = $countryA['risk']->risk_level; $rc = $rl=='High'?'danger':($rl=='Medium'?'warning':'success'); @endphp
                <div>
                    <div class="fs-3 fw-bold">{{ $aRisk }}</div>
                    <span class="badge bg-{{ $rc }}">{{ $rl }}</span>
                </div>
                @else <span class="text-muted small">Belum dihitung</span> @endif
            </div>
            <div class="compare-label">⚠️ Risk Score<br><span style="font-size:0.65rem;opacity:0.7;">lebih rendah = lebih baik</span></div>
            <div class="compare-value {{ $bRisk <= $aRisk ? 'winner' : 'loser' }}">
                @if($countryB['risk'])
                @php $rl = $countryB['risk']->risk_level; $rc = $rl=='High'?'danger':($rl=='Medium'?'warning':'success'); @endphp
                <div>
                    <div class="fs-3 fw-bold">{{ $bRisk }}</div>
                    <span class="badge bg-{{ $rc }}">{{ $rl }}</span>
                </div>
                @else <span class="text-muted small">Belum dihitung</span> @endif
            </div>
        </div>

        {{-- GDP --}}
        @php
            $aGdp = $countryA['economic']?->gdp ?? 0;
            $bGdp = $countryB['economic']?->gdp ?? 0;
        @endphp
        <div class="compare-row">
            <div class="compare-value {{ $aGdp >= $bGdp ? 'winner' : 'loser' }}">
                @if($aGdp)
                <div>
                    <div class="fs-5 fw-bold text-primary">${{ number_format($aGdp/1e9, 1) }}B</div>
                    <small class="text-muted">Tahun {{ $countryA['economic']->year }}</small>
                </div>
                @else <span class="text-muted small">Tidak ada data</span> @endif
            </div>
            <div class="compare-label">📊 GDP<br><span style="font-size:0.65rem;opacity:0.7;">lebih tinggi = lebih baik</span></div>
            <div class="compare-value {{ $bGdp >= $aGdp ? 'winner' : 'loser' }}">
                @if($bGdp)
                <div>
                    <div class="fs-5 fw-bold text-primary">${{ number_format($bGdp/1e9, 1) }}B</div>
                    <small class="text-muted">Tahun {{ $countryB['economic']->year }}</small>
                </div>
                @else <span class="text-muted small">Tidak ada data</span> @endif
            </div>
        </div>

        {{-- Inflasi --}}
        @php
            $aInf = $countryA['economic']?->inflation ?? 9999;
            $bInf = $countryB['economic']?->inflation ?? 9999;
        @endphp
        <div class="compare-row">
            <div class="compare-value {{ ($aInf <= $bInf && $aInf < 9999) ? 'winner' : 'loser' }}">
                @if($countryA['economic']?->inflation !== null)
                @php $ic = $countryA['economic']->inflation > 10 ? 'danger' : ($countryA['economic']->inflation > 5 ? 'warning' : 'success'); @endphp
                <span class="badge bg-{{ $ic }} fs-6">{{ number_format($countryA['economic']->inflation, 1) }}%</span>
                @else <span class="text-muted small">Tidak ada data</span> @endif
            </div>
            <div class="compare-label">📈 Inflasi<br><span style="font-size:0.65rem;opacity:0.7;">lebih rendah = lebih baik</span></div>
            <div class="compare-value {{ ($bInf <= $aInf && $bInf < 9999) ? 'winner' : 'loser' }}">
                @if($countryB['economic']?->inflation !== null)
                @php $ic = $countryB['economic']->inflation > 10 ? 'danger' : ($countryB['economic']->inflation > 5 ? 'warning' : 'success'); @endphp
                <span class="badge bg-{{ $ic }} fs-6">{{ number_format($countryB['economic']->inflation, 1) }}%</span>
                @else <span class="text-muted small">Tidak ada data</span> @endif
            </div>
        </div>

        {{-- Weather --}}
        <div class="compare-row">
            <div class="compare-value">
                @if($countryA['weather'])
                <div class="text-center">
                    <div class="fw-bold">{{ $countryA['weather']->temperature }}°C</div>
                    <small class="text-muted">Wind: {{ $countryA['weather']->wind_speed }} km/h</small><br>
                    @for($i=0;$i<$countryA['weather']->storm_risk;$i++)<span style="color:#f59e0b;">⚡</span>@endfor
                    <small class="text-muted">Storm {{ $countryA['weather']->storm_risk }}/5</small>
                </div>
                @else <span class="text-muted small">Tidak ada data cuaca</span> @endif
            </div>
            <div class="compare-label">🌤️ Cuaca</div>
            <div class="compare-value">
                @if($countryB['weather'])
                <div class="text-center">
                    <div class="fw-bold">{{ $countryB['weather']->temperature }}°C</div>
                    <small class="text-muted">Wind: {{ $countryB['weather']->wind_speed }} km/h</small><br>
                    @for($i=0;$i<$countryB['weather']->storm_risk;$i++)<span style="color:#f59e0b;">⚡</span>@endfor
                    <small class="text-muted">Storm {{ $countryB['weather']->storm_risk }}/5</small>
                </div>
                @else <span class="text-muted small">Tidak ada data cuaca</span> @endif
            </div>
        </div>

        {{-- Currency --}}
        <div class="compare-row">
            <div class="compare-value">
                @if($countryA['exchange'])
                <div class="text-center">
                    <div class="fw-semibold">{{ $countryA['country']->currency }}</div>
                    <div class="text-muted small">1 USD = {{ number_format($countryA['exchange']->exchange_rate, 2) }}</div>
                </div>
                @else
                <div class="text-center">
                    <div class="fw-semibold">{{ $countryA['country']->currency }}</div>
                    <div class="text-muted small">Tidak ada data kurs</div>
                </div>
                @endif
            </div>
            <div class="compare-label">💱 Mata Uang</div>
            <div class="compare-value">
                @if($countryB['exchange'])
                <div class="text-center">
                    <div class="fw-semibold">{{ $countryB['country']->currency }}</div>
                    <div class="text-muted small">1 USD = {{ number_format($countryB['exchange']->exchange_rate, 2) }}</div>
                </div>
                @else
                <div class="text-center">
                    <div class="fw-semibold">{{ $countryB['country']->currency }}</div>
                    <div class="text-muted small">Tidak ada data kurs</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Populasi --}}
        <div class="compare-row">
            <div class="compare-value">
                <span class="fw-semibold">{{ $countryA['economic']?->population ? number_format($countryA['economic']->population/1e6, 1).'M' : '—' }}</span>
            </div>
            <div class="compare-label">👥 Populasi</div>
            <div class="compare-value">
                <span class="fw-semibold">{{ $countryB['economic']?->population ? number_format($countryB['economic']->population/1e6, 1).'M' : '—' }}</span>
            </div>
        </div>

        {{-- Berita --}}
        <div class="compare-row">
            <div class="compare-value">
                <div class="text-center">
                    <div class="small"><span class="badge" style="background:#dcfce7;color:#16a34a;">+{{ $countryA['pos_news'] }}</span>
                    <span class="badge ms-1" style="background:#fee2e2;color:#dc2626;">-{{ $countryA['neg_news'] }}</span></div>
                    <small class="text-muted">{{ $countryA['total_news'] }} berita</small>
                </div>
            </div>
            <div class="compare-label">📰 Berita</div>
            <div class="compare-value">
                <div class="text-center">
                    <div class="small"><span class="badge" style="background:#dcfce7;color:#16a34a;">+{{ $countryB['pos_news'] }}</span>
                    <span class="badge ms-1" style="background:#fee2e2;color:#dc2626;">-{{ $countryB['neg_news'] }}</span></div>
                    <small class="text-muted">{{ $countryB['total_news'] }} berita</small>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Radar Chart Comparison --}}
@if($countryA['risk'] && $countryB['risk'])
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>Risk Components Comparison</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-7 offset-md-2">
                <canvas id="radarCompare" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@endif {{-- end if isset countryA & B --}}

@endsection

@push('scripts')
<script>
@if(isset($countryA) && isset($countryB) && $countryA['risk'] && $countryB['risk'])
new Chart(document.getElementById('radarCompare').getContext('2d'), {
    type: 'radar',
    data: {
        labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk'],
        datasets: [
            {
                label: '{{ $countryA['country']->country_name }}',
                data: [
                    {{ $countryA['risk']->weather_score }},
                    {{ $countryA['risk']->inflation_score }},
                    {{ $countryA['risk']->currency_score }},
                    {{ $countryA['risk']->news_score }},
                ],
                backgroundColor: 'rgba(59,130,246,0.15)',
                borderColor: '#3b82f6',
                pointBackgroundColor: '#3b82f6',
                borderWidth: 2,
            },
            {
                label: '{{ $countryB['country']->country_name }}',
                data: [
                    {{ $countryB['risk']->weather_score }},
                    {{ $countryB['risk']->inflation_score }},
                    {{ $countryB['risk']->currency_score }},
                    {{ $countryB['risk']->news_score }},
                ],
                backgroundColor: 'rgba(139,92,246,0.15)',
                borderColor: '#8b5cf6',
                pointBackgroundColor: '#8b5cf6',
                borderWidth: 2,
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            r: { beginAtZero: true, max: 25,
                 ticks: { stepSize: 5, font: { size: 11 } },
                 grid: { color: '#f1f5f9' }
            }
        },
        plugins: { legend: { position: 'bottom', labels: { padding: 16 } } }
    }
});
@endif
</script>
@endpush
