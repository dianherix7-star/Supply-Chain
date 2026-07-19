@extends('layouts.app')

@section('page-title', 'Weather Data')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">☁️ Weather Data</h4>
        <small class="text-muted">Data cuaca real-time dari <strong>Open-Meteo API</strong> (gratis, tanpa API key)</small>
    </div>
    @if(Auth::user()->isAdmin())
    <form action="{{ route('weather.fetch-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary"
            onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Fetching...';this.disabled=true;this.form.submit();">
            <i class="bi bi-cloud-download me-1"></i> Fetch Semua Negara
        </button>
    </form>
    @endif
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
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #0d6efd">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small"><i class="bi bi-globe me-1"></i>Total Negara</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #fd7e14">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['avg_temp'] ?? '—' }}°C</div>
                <div class="text-muted small"><i class="bi bi-thermometer-half me-1"></i>Rata-rata Suhu</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #0dcaf0">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-info">{{ $stats['avg_wind'] ?? '—' }} km/h</div>
                <div class="text-muted small"><i class="bi bi-wind me-1"></i>Rata-rata Angin</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #dc3545">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger">{{ $stats['high_storm'] }}</div>
                <div class="text-muted small"><i class="bi bi-lightning-charge me-1"></i>Risiko Badai Tinggi</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('weather.index') }}" class="row g-2 align-items-center">
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
                <select name="region" class="form-select">
                    <option value="">Semua Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                @if(request('search') || request('region'))
                    <a href="{{ route('weather.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
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
                    <th>Region</th>
                    <th>🌡️ Suhu</th>
                    <th>🌧️ Curah Hujan</th>
                    <th>💨 Angin</th>
                    <th>⚡ Risiko Badai</th>
                    <th>Diperbarui</th>
                    <th class="text-center" width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>

            @forelse($weatherData as $item)
                <tr>
                    <td class="ps-4 text-muted">{{ $weatherData->firstItem() + $loop->index }}</td>

                    <td class="text-center">
                        @if($item->country->flag_url)
                            <img src="{{ $item->country->flag_url }}" width="40" height="27"
                                 style="border:1px solid #e2e8f0;border-radius:3px;object-fit:cover;"
                                 alt="{{ $item->country->country_name }}"
                                 loading="lazy">
                        @endif
                    </td>

                    <td>
                        <div class="fw-semibold">{{ $item->country->country_name }}</div>
                        <small class="text-muted">{{ $item->country->country_code }}</small>
                    </td>

                    <td>
                        <span class="badge rounded-pill
                            @if($item->country->region == 'Asia') bg-warning text-dark
                            @elseif($item->country->region == 'Europe') bg-info text-dark
                            @elseif($item->country->region == 'Africa') bg-success
                            @elseif($item->country->region == 'Americas') bg-danger
                            @elseif($item->country->region == 'Oceania') bg-primary
                            @else bg-secondary @endif">
                            {{ $item->country->region ?? '—' }}
                        </span>
                    </td>

                    <td>
                        @if(!is_null($item->temperature))
                            @php
                                $temp = $item->temperature;
                                $tempClass = $temp >= 35 ? 'text-danger fw-bold' : ($temp <= 5 ? 'text-info' : 'text-dark');
                            @endphp
                            <span class="{{ $tempClass }}">{{ number_format($temp, 1) }}°C</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-muted">
                        {{ number_format($item->rainfall ?? 0, 1) }} mm
                    </td>

                    <td class="text-muted">
                        {{ number_format($item->wind_speed ?? 0, 1) }} km/h
                    </td>

                    <td>
                        @php
                            $risk = $item->storm_risk ?? 0;
                            $stormLabel = ['Aman', 'Kabut', 'Hujan', 'Deras', 'Badai', 'Badai Besar'];
                            $stormClass = ['success', 'secondary', 'info', 'warning', 'danger', 'danger'];
                        @endphp
                        <div class="d-flex align-items-center gap-1">
                            @for($i = 0; $i < 5; $i++)
                                <div style="width:10px;height:10px;border-radius:2px;
                                    background: {{ $i < $risk ? '#dc3545' : '#e9ecef' }}"></div>
                            @endfor
                            <span class="badge bg-{{ $stormClass[$risk] ?? 'secondary' }} ms-1">
                                {{ $stormLabel[$risk] ?? '—' }}
                            </span>
                        </div>
                    </td>

                    <td class="text-muted small">
                        {{ $item->recorded_at ? \Carbon\Carbon::parse($item->recorded_at)->diffForHumans() : '—' }}
                    </td>

                    <td class="text-center">
                        <form action="{{ route('weather.fetch-one', $item->country->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm" title="Refresh data cuaca">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-cloud-slash display-4 d-block mb-2 opacity-25"></i>
                        Belum ada data cuaca.
                        <br><small>Klik <strong>"Fetch Semua Negara"</strong> untuk mengambil data cuaca dari Open-Meteo.</small>
                        <br><small class="text-muted mt-1 d-block">Pastikan sudah import data countries terlebih dahulu.</small>
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    @if($weatherData->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $weatherData->firstItem() }}–{{ $weatherData->lastItem() }}
            dari {{ $weatherData->total() }} data
        </small>
        {{ $weatherData->links() }}
    </div>
    @endif
</div>

@endsection
