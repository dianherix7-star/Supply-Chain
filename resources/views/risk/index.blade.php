@extends('layouts.app')

@section('page-title', 'Risk Score Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">⚡ Risk Score Dashboard</h4>
        <small class="text-muted">Kalkulasi risiko supply chain berdasarkan cuaca, mata uang, inflasi, dan berita</small>
    </div>
    <form action="{{ route('risk.calculate-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary"
            onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Menghitung...';this.disabled=true;this.form.submit();">
            <i class="bi bi-calculator me-1"></i> Hitung Semua Risk Score
        </button>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #0d6efd">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total Dihitung</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #fd7e14">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold" style="color:#fd7e14">{{ $stats['avg_score'] ?? 0 }}</div>
                <div class="text-muted small">Rata-rata Skor</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #dc3545">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger">{{ $stats['high'] }}</div>
                <div class="text-muted small">🔴 High Risk</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #ffc107">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['medium'] }}</div>
                <div class="text-muted small">🟡 Medium Risk</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #198754">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['low'] }}</div>
                <div class="text-muted small">🟢 Low Risk</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center" style="border-top:3px solid #6f42c1">
            <div class="card-body py-3">
                @php
                    $total = $stats['total'] ?: 1;
                    $highPct = round(($stats['high'] / $total) * 100);
                @endphp
                <div class="fs-2 fw-bold" style="color:#6f42c1">{{ $highPct }}%</div>
                <div class="text-muted small">% High Risk</div>
            </div>
        </div>
    </div>
</div>

<!-- Risk Distribution Bar -->
@if($stats['total'] > 0)
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="fw-semibold small">Distribusi Risiko</span>
        </div>
        @php
            $total = $stats['total'] ?: 1;
            $lowPct = round(($stats['low'] / $total) * 100);
            $medPct = round(($stats['medium'] / $total) * 100);
            $hiPct  = round(($stats['high'] / $total) * 100);
        @endphp
        <div class="progress" style="height:28px;border-radius:8px;">
            <div class="progress-bar bg-success" style="width:{{ $lowPct }}%">
                Low {{ $lowPct }}%
            </div>
            <div class="progress-bar bg-warning text-dark" style="width:{{ $medPct }}%">
                Medium {{ $medPct }}%
            </div>
            <div class="progress-bar bg-danger" style="width:{{ $hiPct }}%">
                High {{ $hiPct }}%
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('risk.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Cari nama negara..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="risk_level" class="form-select">
                    <option value="">Semua Level</option>
                    <option value="High" {{ request('risk_level') == 'High' ? 'selected' : '' }}>🔴 High</option>
                    <option value="Medium" {{ request('risk_level') == 'Medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="Low" {{ request('risk_level') == 'Low' ? 'selected' : '' }}>🟢 Low</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                @if(request('search') || request('risk_level'))
                    <a href="{{ route('risk.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-4" width="50">No</th>
                    <th width="60">Flag</th>
                    <th>Negara</th>
                    <th class="text-center">🌡️ Cuaca<br><small class="fw-normal opacity-75">/25</small></th>
                    <th class="text-center">📈 Inflasi<br><small class="fw-normal opacity-75">/25</small></th>
                    <th class="text-center">💱 Kurs<br><small class="fw-normal opacity-75">/25</small></th>
                    <th class="text-center">📰 Berita<br><small class="fw-normal opacity-75">/25</small></th>
                    <th class="text-center">Total<br><small class="fw-normal opacity-75">/100</small></th>
                    <th class="text-center">Level</th>
                    <th class="text-center" width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($riskScores as $risk)
                <tr>
                    <td class="ps-4 text-muted">{{ $riskScores->firstItem() + $loop->index }}</td>

                    <td class="text-center">
                        @if($risk->country && $risk->country->flag_url)
                            <img src="{{ $risk->country->flag_url }}" width="40" height="27"
                                 style="border:1px solid #e2e8f0;border-radius:3px;object-fit:cover;"
                                 alt="{{ $risk->country->country_name }}"
                                 loading="lazy">
                        @endif
                    </td>

                    <td>
                        <div class="fw-semibold">{{ $risk->country->country_name ?? '—' }}</div>
                        <small class="text-muted">{{ $risk->country->region ?? '' }}</small>
                    </td>

                    {{-- Score bars per component --}}
                    @foreach(['weather_score', 'inflation_score', 'currency_score', 'news_score'] as $scoreField)
                    <td class="text-center">
                        @php
                            $val = $risk->$scoreField;
                            $pct = ($val / 25) * 100;
                            $barColor = $pct >= 70 ? '#dc3545' : ($pct >= 40 ? '#ffc107' : '#198754');
                        @endphp
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <div style="width:40px;height:6px;background:#e9ecef;border-radius:3px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};border-radius:3px;"></div>
                            </div>
                            <small class="fw-semibold" style="min-width:20px;">{{ $val }}</small>
                        </div>
                    </td>
                    @endforeach

                    <td class="text-center">
                        @php
                            $total = $risk->total_score;
                            $totalColor = $total >= 71 ? 'danger' : ($total >= 41 ? 'warning' : 'success');
                        @endphp
                        <span class="badge bg-{{ $totalColor }} fs-6 px-3">{{ $total }}</span>
                    </td>

                    <td class="text-center">
                        @php
                            $levelConfig = match($risk->risk_level) {
                                'High'   => ['danger',  'exclamation-triangle-fill', '🔴'],
                                'Medium' => ['warning', 'exclamation-circle-fill', '🟡'],
                                'Low'    => ['success', 'check-circle-fill', '🟢'],
                                default  => ['secondary', 'question-circle', '⚪'],
                            };
                        @endphp
                        <span class="badge bg-{{ $levelConfig[0] }}">
                            {{ $levelConfig[2] }} {{ $risk->risk_level }}
                        </span>
                    </td>

                    <td class="text-center">
                        <form action="{{ route('risk.calculate-one', $risk->country->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm" title="Recalculate">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-calculator display-4 d-block mb-2 opacity-25"></i>
                        Belum ada data risk score.
                        <br><small>Klik <strong>"Hitung Semua Risk Score"</strong> untuk kalkulasi.</small>
                        <br><small class="text-muted mt-1 d-block">
                            Pastikan sudah fetch data <strong>Weather</strong>, <strong>Currency</strong>,
                            dan <strong>News</strong> terlebih dahulu untuk hasil yang akurat.
                        </small>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($riskScores->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $riskScores->firstItem() }}–{{ $riskScores->lastItem() }}
            dari {{ $riskScores->total() }}
        </small>
        {{ $riskScores->links() }}
    </div>
    @endif
</div>

<!-- Formula Info -->
<div class="card mt-3">
    <div class="card-header bg-light fw-semibold">📐 Formula Perhitungan Risk Score</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 rounded bg-light">
                    <div class="fw-semibold mb-1">🌡️ Weather Score <span class="text-muted">(0–25)</span></div>
                    <small class="text-muted">Storm risk × 3 + bonus dari wind speed & suhu ekstrem</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded bg-light">
                    <div class="fw-semibold mb-1">📈 Inflation Score <span class="text-muted">(0–25)</span></div>
                    <small class="text-muted">Dari data inflasi ekonomi, atau estimasi berdasarkan region</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded bg-light">
                    <div class="fw-semibold mb-1">💱 Currency Score <span class="text-muted">(0–25)</span></div>
                    <small class="text-muted">Kekuatan mata uang vs USD — semakin lemah, semakin tinggi skor risiko</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded bg-light">
                    <div class="fw-semibold mb-1">📰 News Score <span class="text-muted">(0–25)</span></div>
                    <small class="text-muted">Rasio berita negatif vs total — semakin banyak negatif, semakin tinggi</small>
                </div>
            </div>
        </div>
        <div class="mt-3 text-center text-muted small">
            <strong>Total Score (0–100)</strong> = Weather + Inflation + Currency + News |
            <span class="badge bg-success">Low: 0–40</span>
            <span class="badge bg-warning text-dark">Medium: 41–70</span>
            <span class="badge bg-danger">High: 71–100</span>
        </div>
    </div>
</div>

@endsection
