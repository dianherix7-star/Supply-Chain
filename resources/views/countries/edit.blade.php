@extends('layouts.app')

@section('content')

<div class="card shadow">

    <div class="card-header bg-warning">
        <h4>Edit Country</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('countries.update',$country->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Country Name</label>
                <input type="text"
                       name="country_name"
                       value="{{ $country->country_name }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Country Code</label>
                <input type="text"
                       name="country_code"
                       value="{{ $country->country_code }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Capital</label>
                <input type="text"
                       name="capital"
                       value="{{ $country->capital }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Region</label>
                <input type="text"
                       name="region"
                       value="{{ $country->region }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Population</label>
                <input type="number"
                       name="population"
                       value="{{ $country->population }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Currency</label>
                <input type="text"
                       name="currency"
                       value="{{ $country->currency }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Flag</label>
                <input type="text"
                       name="flag"
                       value="{{ $country->flag }}"
                       class="form-control">
            </div>

            <button class="btn btn-primary">
                Update
            </button>

            <a href="{{ route('countries.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </form>

    </div>

</div>

@endsection