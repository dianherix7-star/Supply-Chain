@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-4">

    <h2>Country Management</h2>

    <div>

        <a href="{{ route('countries.import') }}"
            class="btn btn-success">
            Import Countries
        </a>

        <a href="{{ route('countries.create') }}"
            class="btn btn-primary">
            + Tambah Country
        </a>

    </div>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

@if(session('error'))

<div class="alert alert-danger">
    {{ session('error') }}
</div>

@endif


<div class="card shadow">

    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th width="60">No</th>
                    <th width="80">Flag</th>
                    <th>Country</th>
                    <th>Code</th>
                    <th>Capital</th>
                    <th>Region</th>
                    <th width="170">Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($countries as $country)

                <tr>

                    <td>
                        {{ $countries->firstItem() + $loop->index }}
                    </td>

                    <td class="text-center">

                        @if($country->flag)
                            <img
                                src="{{ $country->flag }}"
                                width="50"
                                height="35"
                                alt="Flag"
                                style="border:1px solid #000;">

                        @else

                            -

                        @endif

                    </td>

                    <td>{{ $country->country_name }}</td>

                    <td>{{ $country->country_code }}</td>

                    <td>{{ $country->capital }}</td>

                    <td>{{ $country->region }}</td>

                    <td>

                        <a href="{{ route('countries.edit',$country->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('countries.destroy',$country->id) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        Tidak ada data Country.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $countries->links() }}

        </div>

    </div>

</div>

@endsection