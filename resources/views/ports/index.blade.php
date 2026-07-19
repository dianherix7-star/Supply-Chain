@extends('layouts.app')

@section('page-title', 'Port Location Dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #port-map { 
        height: 500px; 
        border-radius: 12px; 
        z-index: 1; 
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }
    .leaflet-popup-content { min-width: 180px; }
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">🚢 Port Location Dashboard</h4>
        <small class="text-muted">
            Peta interaktif lokasi pelabuhan dunia —
            <strong>{{ $stats['total'] }}</strong> pelabuhan di
            <strong>{{ $stats['countries'] }}</strong> negara
            (<strong>{{ $stats['with_coords'] }}</strong> memiliki koordinat)
        </small>
        <div class="mt-2">
            <span class="badge bg-light text-dark border" style="font-size:0.7rem;">
                <i class="bi bi-globe me-1 text-primary"></i>Overpass API (OpenStreetMap) — Gratis, Tanpa API Key
            </span>
        </div>
    </div>
    @if(Auth::user()->isAdmin())
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        <!-- Fetch dari Overpass API (OSM) -->
        <form action="{{ route('ports.fetch-osm') }}" method="POST"
              onsubmit="this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Mengambil data...'; this.querySelector('button').disabled=true;">
            @csrf
            <button type="submit" class="btn btn-outline-primary"
                    title="Fetch ratusan pelabuhan dunia dari Overpass API (OpenStreetMap) — gratis tanpa API key">
                <i class="bi bi-cloud-download me-1"></i>
                Fetch dari OSM (Global)
            </button>
        </form>
        <a href="{{ route('ports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Manual
        </a>
    </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- PETA LEAFLET.JS -->
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header fw-bold bg-white border-bottom text-dark d-flex justify-content-between align-items-center">
        <span><i class="bi bi-map me-2 text-primary"></i>Peta Pelabuhan Dunia</span>
        <small class="text-muted" style="font-size:0.75rem;">Klik marker untuk detail pelabuhan</small>
    </div>
    <div class="card-body p-2">
        <div id="port-map"></div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-3 border-0 shadow-sm bg-white">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('ports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           style="border-color: #cbd5e1;"
                           placeholder="Cari nama pelabuhan..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="country_id" class="form-select border" style="border-color: #cbd5e1 !important;">
                    <option value="">Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                @if(request('search') || request('country_id'))
                    <a href="{{ route('ports.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Fetch per Negara dari OSM -->
<div class="card mb-3 border-0 shadow-sm bg-white" style="border-left: 4px solid #10b981 !important;">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="fw-bold text-dark small">
                    <i class="bi bi-geo-alt-fill text-success me-1"></i>
                    Fetch Pelabuhan Per Negara dari <strong>Overpass API (OpenStreetMap)</strong>
                </div>
                <small class="text-muted">Gratis, tanpa API key — data real dari komunitas OSM global</small>
            </div>
            <form action="#" method="POST" id="fetchByCountryForm">
                @csrf
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-select form-select-sm border" id="osmCountrySelect" style="min-width:200px; border-color: #cbd5e1 !important;" required>
                        <option value="">Pilih Negara...</option>
                        @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary" id="fetchByCountryBtn">
                        <i class="bi bi-cloud-download me-1"></i> Fetch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Tabel Pelabuhan -->
<div class="card border-0 shadow-sm bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="50">No</th>
                        <th width="60">Flag</th>
                        <th>Pelabuhan</th>
                        <th>Negara</th>
                        <th>Koordinat</th>
                        @if(Auth::user()->isAdmin())
                        <th class="text-center" width="120">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @forelse($ports as $port)
                    <tr>
                        <td class="ps-4 text-muted">{{ $ports->firstItem() + $loop->index }}</td>
                        <td>
                            @if($port->country)
                                @if($port->country->country_code)
                                    <img src="https://flagcdn.com/w40/{{ strtolower($port->country->country_code) }}.png"
                                         width="40" height="27"
                                         style="border:1px solid #e2e8f0;border-radius:3px;object-fit:cover;display:block;"
                                         alt="{{ $port->country->country_name }}"
                                         loading="lazy">
                                @elseif($port->country->flag)
                                    <span style="font-size:1.6rem;line-height:1;" title="{{ $port->country->country_name }}">{!! $port->country->flag !!}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold"><i class="bi bi-anchor text-primary me-1"></i>{{ $port->port_name }}</div>
                        </td>
                        <td class="text-muted">{{ $port->country->country_name ?? '—' }}</td>
                        <td>
                            @if($port->latitude && $port->longitude)
                                <small class="text-muted font-monospace">
                                    {{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}
                                </small>
                                <a href="https://maps.google.com/?q={{ $port->latitude }},{{ $port->longitude }}"
                                   target="_blank" class="ms-1 text-decoration-none">
                                    <i class="bi bi-box-arrow-up-right text-primary" style="font-size:0.75rem;"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        @if(Auth::user()->isAdmin())
                        <td class="text-center">
                            <a href="{{ route('ports.edit', $port->id) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('ports.destroy', $port->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus pelabuhan {{ $port->port_name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm px-2 py-1" style="border-radius: 8px;" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isAdmin() ? 6 : 5 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-anchor display-4 d-block mb-2 opacity-25"></i>
                            Belum ada data pelabuhan.
                            <br><small>Jalankan: <code>php artisan db:seed --class=PortSeeder</code></small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ports->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">Menampilkan {{ $ports->firstItem() }}–{{ $ports->lastItem() }} dari {{ $ports->total() }}</small>
        {{ $ports->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Inisialisasi peta Leaflet.js
const map = L.map('port-map').setView([20, 0], 2);

// Tile layer OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
}).addTo(map);

// Data pelabuhan dari Laravel
const portsData = @json($allPortsForMap);

// Custom icon - Beautiful primary circle pin with anchor inside
const portIcon = L.divIcon({
    html: `
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary border border-2 border-white text-white shadow-sm" 
             style="width: 28px; height: 28px; transition: transform 0.2s ease-in-out;">
            <i class="bi bi-anchor" style="font-size: 14px;"></i>
        </div>
    `,
    iconSize: [28, 28],
    iconAnchor: [14, 14],
    className: 'custom-div-icon'
});

// Tambah marker untuk setiap pelabuhan
const markers = [];

portsData.forEach(port => {
    if (port.latitude && port.longitude) {
        const marker = L.marker([port.latitude, port.longitude], { icon: portIcon })
            .addTo(map);

        const flagUrl = port.country
            ? `https://flagcdn.com/w40/${(port.country.country_code || '').toLowerCase()}.png`
            : '';

        marker.bindPopup(`
            <div style="min-width:180px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    ${flagUrl ? `<img src="${flagUrl}" width="28" height="19" style="border-radius:2px;border:1px solid #ddd;">` : ''}
                    <strong>${port.port_name}</strong>
                </div>
                <div class="text-muted small">
                    <i class="bi bi-globe me-1"></i>${port.country ? port.country.country_name : '—'}
                </div>
                <div class="text-muted small mt-1">
                    📍 ${port.latitude.toFixed(4)}, ${port.longitude.toFixed(4)}
                </div>
            </div>
        `);
        
        markers.push(marker);
    }
});

// Otomatis atur zoom & fokus peta sesuai sebaran penanda (markers) pelabuhan
if (markers.length === 1) {
    map.setView(markers[0].getLatLng(), 6);
} else if (markers.length > 1) {
    const group = L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.15));
}

// Handler: Fetch per Negara dari OSM
document.getElementById('fetchByCountryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const countryId = document.getElementById('osmCountrySelect').value;
    if (!countryId) { alert('Pilih negara terlebih dahulu.'); return; }

    const btn = document.getElementById('fetchByCountryBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengambil...';
    btn.disabled = true;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/ports/fetch-osm/${countryId}`;
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
    document.body.appendChild(form);
    form.submit();
});
</script>
@endpush
