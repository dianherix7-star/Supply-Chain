@extends('layouts.app')

@section('page-title', 'Country Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1 fw-bold">🌍 Country Management</h4>
        <small class="text-muted">Total: {{ $countries->total() }} negara tersimpan</small>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('countries.import') }}"
           class="btn btn-success"
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
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('countries.index') }}" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control border-start-0 ps-0"
                       placeholder="Cari nama negara, kode, atau region..."
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
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
                        @if($country->flag)
                            <img src="{{ $country->flag }}"
                                 width="48" height="32"
                                 alt="{{ $country->country_code }}"
                                 style="border:1px solid #e2e8f0;border-radius:4px;object-fit:cover;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        <div class="fw-semibold">{{ $country->country_name }}</div>
                    </td>

                    <td>
                        <span class="badge bg-light text-dark border">{{ $country->country_code }}</span>
                    </td>

                    <td class="text-muted">{{ $country->capital ?? '—' }}</td>

                    <td>
                        @if($country->region)
                            <span class="badge rounded-pill
                                @if($country->region == 'Asia') bg-warning text-dark
                                @elseif($country->region == 'Europe') bg-info text-dark
                                @elseif($country->region == 'Africa') bg-success
                                @elseif($country->region == 'Americas') bg-danger
                                @elseif($country->region == 'Oceania') bg-primary
                                @else bg-secondary
                                @endif">
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
                           class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form action="{{ route('countries.destroy', $country->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus {{ $country->country_name }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Hapus">
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

    @if($countries->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $countries->firstItem() }}–{{ $countries->lastItem() }}
            dari {{ $countries->total() }} negara
        </small>
        {{ $countries->links() }}
    </div>
    @endif

</div>

@endsection