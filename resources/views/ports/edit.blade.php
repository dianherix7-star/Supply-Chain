@extends('layouts.app')
@section('page-title', 'Edit Pelabuhan')
@section('content')
<div class="card shadow-sm" style="max-width:600px;">
    <div class="card-header bg-warning fw-semibold">
        <i class="bi bi-pencil me-2"></i>Edit Pelabuhan
    </div>
    <div class="card-body">
        <form action="{{ route('ports.update', $port->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Negara <span class="text-danger">*</span></label>
                <select name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                    <option value="">— Pilih Negara —</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $port->country_id == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Pelabuhan <span class="text-danger">*</span></label>
                <input type="text" name="port_name" value="{{ old('port_name', $port->port_name) }}"
                       class="form-control @error('port_name') is-invalid @enderror" required>
                @error('port_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Latitude</label>
                    <input type="number" step="0.000001" name="latitude"
                           value="{{ old('latitude', $port->latitude) }}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Longitude</label>
                    <input type="number" step="0.000001" name="longitude"
                           value="{{ old('longitude', $port->longitude) }}" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-lg me-1"></i>Update
                </button>
                <a href="{{ route('ports.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
