@extends('layouts.app')
@section('page-title', 'Tambah Pelabuhan')
@section('content')
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-anchor me-2"></i>Tambah Pelabuhan Baru
    </div>
    <div class="card-body">
        <form action="{{ route('ports.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Negara <span class="text-danger">*</span></label>
                <select name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                    <option value="">— Pilih Negara —</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Pelabuhan <span class="text-danger">*</span></label>
                <input type="text" name="port_name" value="{{ old('port_name') }}"
                       class="form-control @error('port_name') is-invalid @enderror"
                       placeholder="Contoh: Port of Singapore" required>
                @error('port_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Latitude</label>
                    <input type="number" step="0.000001" name="latitude" value="{{ old('latitude') }}"
                           class="form-control" placeholder="-6.1057">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Longitude</label>
                    <input type="number" step="0.000001" name="longitude" value="{{ old('longitude') }}"
                           class="form-control" placeholder="106.8783">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
                <a href="{{ route('ports.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
