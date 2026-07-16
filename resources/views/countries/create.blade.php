@extends('layouts.app')

@section('content')

<div class="card shadow">

    <div class="card-header bg-primary text-white">
        <h4>Tambah Country</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('countries.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label>Country Name</label>
                <input type="text" name="country_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Country Code</label>
                <input type="text" name="country_code" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Capital</label>
                <input type="text" name="capital" class="form-control">
            </div>

            <div class="mb-3">
                <label>Region</label>
                <input type="text" name="region" class="form-control">
            </div>

            <div class="mb-3">
                <label>Population</label>
                <input type="number" name="population" class="form-control">
            </div>

            <div class="mb-3">
                <label>Currency</label>
                <input type="text" name="currency" class="form-control">
            </div>

            <div class="mb-3">
                <label>Flag (URL)</label>
                <input type="text" name="flag" class="form-control">
            </div>

            <button class="btn btn-success">
                Simpan
            </button>

            <a href="{{ route('countries.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </form>

    </div>

</div>

@endsection