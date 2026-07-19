@extends('layouts.app')

@section('title', 'Economic Data')
@section('page-title', 'Economic Data — World Bank')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="section-title">📈 Economic Data</h1>
        <p class="section-sub">Data ekonomi dari World Bank API: GDP, Inflasi, Populasi, Ekspor, Impor</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('economic.fetch-all') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-primary" onclick="return confirm('Fetch data dari World Bank? (30 negara pertama)')">
                <i class="bi bi-cloud-download me-1"></i> Fetch Semua (30 Negara)
            </button>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-primary fw-bold fs-2">{{ $stats['total'] }}</div>
                <div class="text-muted small">Negara dengan Data</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                @php $avgGdp = $stats['avg_gdp'] ? '$' . number_format($stats['avg_gdp']/1e9, 1) . 'B' : '-'; @endphp
                <div class="text-success fw-bold fs-2">{{ $avgGdp }}</div>
                <div class="text-muted small">Avg GDP</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-warning fw-bold fs-2">{{ $stats['avg_inf'] }}%</div>
                <div class="text-muted small">Avg Inflasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="text-info fw-bold fs-2">{{ $stats['latest_year'] ?? '-' }}</div>
                <div class="text-muted small">Tahun Terbaru</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('economic.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Cari negara..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                @if(request()->anyFilled(['search','year']))
                <a href="{{ route('economic.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
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
                    <th>Negara</th>
                    <th class="text-end">GDP (USD)</th>
                    <th class="text-center">Inflasi %</th>
                    <th class="text-end">Populasi</th>
                    <th class="text-end">Ekspor (USD)</th>
                    <th class="text-end">Impor (USD)</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($economicData as $eco)
            <tr>
                <td class="ps-4 text-muted">{{ $economicData->firstItem() + $loop->index }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if($eco->country?->flag_url)
                        <img src="{{ $eco->country->flag_url }}" width="32" height="21"
                             style="border-radius:3px;border:1px solid #e2e8f0;object-fit:cover;"
                             alt="{{ $eco->country->country_name }}"
                             loading="lazy">
                        @endif
                        <div>
                            <div class="fw-semibold small">{{ $eco->country?->country_name ?? '—' }}</div>
                            <div class="text-muted" style="font-size:0.7rem;">{{ $eco->country?->region }}</div>
                        </div>
                    </div>
                </td>
                <td class="text-end">
                    @if($eco->gdp)
                    <span class="fw-semibold text-primary">${{ number_format($eco->gdp / 1e9, 1) }}B</span>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($eco->inflation !== null)
                    @php $infColor = $eco->inflation > 10 ? 'danger' : ($eco->inflation > 5 ? 'warning' : 'success'); @endphp
                    <span class="badge bg-{{ $infColor }}">{{ number_format($eco->inflation, 1) }}%</span>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-end text-muted small">
                    {{ $eco->population ? number_format($eco->population / 1e6, 1) . 'M' : '—' }}
                </td>
                <td class="text-end text-muted small">
                    {{ $eco->exports ? '$' . number_format($eco->exports / 1e9, 1) . 'B' : '—' }}
                </td>
                <td class="text-end text-muted small">
                    {{ $eco->imports ? '$' . number_format($eco->imports / 1e9, 1) . 'B' : '—' }}
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $eco->year }}</span>
                </td>
                <td class="text-center">
                    @if($eco->country)
                    <form action="{{ route('economic.fetch-one', $eco->country) }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm" title="Refresh dari World Bank">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
                    <i class="bi bi-graph-up display-4 d-block mb-2 opacity-25"></i>
                    Belum ada data ekonomi.<br>
                    <small>Klik <strong>"Fetch Semua"</strong> untuk mengambil data dari World Bank API.</small>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($economicData->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $economicData->firstItem() }}–{{ $economicData->lastItem() }} dari {{ $economicData->total() }}</small>
        {{ $economicData->links() }}
    </div>
    @endif
</div>

<!-- Fetch per negara -->
<div class="card mt-3">
    <div class="card-header"><i class="bi bi-cloud-download me-2"></i>Fetch Data per Negara</div>
    <div class="card-body">
        <form action="#" method="POST" id="fetchOneForm">
            @csrf
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <select class="form-select" id="fetchCountrySelect" required>
                        <option value="">Pilih Negara...</option>
                        @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->country_name }} ({{ $c->country_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-cloud-download me-1"></i> Fetch Negara Ini
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('fetchOneForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const countryId = document.getElementById('fetchCountrySelect').value;
    if (!countryId) { alert('Pilih negara terlebih dahulu.'); return; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/economic/fetch/${countryId}`;
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
    document.body.appendChild(form);
    form.submit();
});
</script>
@endpush
