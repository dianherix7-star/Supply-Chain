@extends('layouts.app')

@section('page-title', 'Country Management')

@push('styles')
<style>
    .country-link {
        color: var(--text-main);
        transition: var(--transition);
    }
    .country-link:hover {
        color: var(--primary) !important;
    }
    .badge-region {
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
    }
    .region-asia      { background: #fffbeb; color: #d97706; border: 1px solid rgba(245, 158, 11, 0.12); }
    .region-europe    { background: #f0f9ff; color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.12); }
    .region-africa    { background: #f0fdf4; color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.12); }
    .region-americas  { background: #fdf2f8; color: #db2777; border: 1px solid rgba(219, 39, 119, 0.12); }
    .region-oceania   { background: #f5f3ff; color: #7c3aed; border: 1px solid rgba(124, 58, 237, 0.12); }
    .region-default   { background: #f8fafc; color: #64748b; border: 1px solid rgba(100, 116, 139, 0.12); }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">🌍 Country Management</h4>
        <small class="text-muted">Total: {{ $countries->total() }} negara tersimpan</small>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('countries.import') }}"
           class="btn btn-outline-primary"
           onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Importing...';this.classList.add('disabled');">
            <i class="bi bi-cloud-download me-1"></i> Import dari API
        </a>
        <a href="{{ route('countries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Manual
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Search Bar -->
<div class="card mb-3 shadow-sm border-0 bg-white">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('countries.index') }}" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control border-start-0 ps-0"
                       style="border-color: #cbd5e1;"
                       placeholder="Cari nama negara, kode, atau region..."
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="50">No</th>
                        <th width="70">Flag</th>
                        <th>Negara</th>
                        <th width="70">Kode</th>
                        <th>Ibu Kota</th>
                        <th>Region</th>
                        <th>Populasi</th>
                        <th>Mata Uang</th>
                        <th class="text-center" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($countries as $country)
                    <tr>
                        <td class="ps-4 text-muted">
                            {{ $countries->firstItem() + $loop->index }}
                        </td>

                        <td class="text-center">
                            @if($country->country_code)
                                <img src="https://flagcdn.com/w40/{{ strtolower($country->country_code) }}.png"
                                     width="48" height="32"
                                     alt="{{ $country->country_name }}"
                                     style="border:1px solid #e2e8f0;border-radius:4px;object-fit:cover;display:inline-block;"
                                     loading="lazy"
                                     onerror="this.replaceWith(document.createTextNode('{{ $country->flag }}'))">
                            @elseif($country->flag)
                                <span style="font-size:1.6rem;line-height:1;">{!! $country->flag !!}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('countries.show', $country->id) }}" class="fw-semibold text-decoration-none country-link">{{ $country->country_name }}</a>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">{{ $country->country_code }}</span>
                        </td>

                        <td class="text-muted">{{ $country->capital ?? '—' }}</td>

                        <td>
                            @if($country->region)
                                @php
                                    $reg = strtolower($country->region);
                                    $class = match($reg) {
                                        'asia' => 'region-asia',
                                        'europe' => 'region-europe',
                                        'africa' => 'region-africa',
                                        'americas' => 'region-americas',
                                        'oceania' => 'region-oceania',
                                        default => 'region-default'
                                    };
                                @endphp
                                <span class="badge-region {{ $class }}">
                                    {{ $country->region }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-muted">
                            @if($country->population)
                                {{ number_format($country->population) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-muted">
                            <small>{{ $country->currency ?? '—' }}</small>
                        </td>

                        <td class="text-center">
                            <a href="{{ route('countries.edit', $country->id) }}"
                               class="btn btn-outline-secondary btn-sm px-2 py-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('countries.destroy', $country->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus {{ $country->country_name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm px-2 py-1" style="border-radius: 8px;" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-globe display-4 d-block mb-2 opacity-25"></i>
                            Tidak ada data negara.
                            @if(request('search'))
                                <br><small>Coba kata kunci lain atau <a href="{{ route('countries.index') }}">reset filter</a>.</small>
                            @else
                                <br><small>Klik <strong>"Import dari API"</strong> untuk mengambil data ~250 negara secara otomatis.</small>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($countries->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">
            Menampilkan {{ $countries->firstItem() }}–{{ $countries->lastItem() }}
            dari {{ $countries->total() }} negara
        </small>
        {{ $countries->links() }}
    </div>
    @endif
</div>

@endsection