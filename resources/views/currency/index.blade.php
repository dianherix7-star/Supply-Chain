@extends('layouts.app')

@section('page-title', 'Currency / Exchange Rate')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">💱 Currency / Exchange Rate</h4>
        <small class="text-muted">Data kurs mata uang dari <strong>Frankfurter API</strong> — Base: <strong>USD</strong></small>
    </div>
    @if(Auth::user()->isAdmin())
    <form action="{{ route('currency.fetch-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary"
            onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Fetching...';this.disabled=true;this.form.submit();">
            <i class="bi bi-cloud-download me-1"></i> Fetch Exchange Rates
        </button>
    </form>
    @endif
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

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top:4px solid #6366f1 !important">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1">Total Mata Uang</div>
                <div class="fs-2 fw-extrabold text-primary" style="font-weight:800;">{{ $stats['total_currencies'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top:4px solid #10b981 !important">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1">Terkuat vs USD</div>
                <div class="fs-4 fw-bold text-success">
                    {{ $stats['strongest'] ? $stats['strongest']->currency : '—' }}
                </div>
                @if($stats['strongest'])
                    <small class="text-success fw-semibold">{{ number_format($stats['strongest']->exchange_rate, 4) }}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top:4px solid #ef4444 !important">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1">Terlemah vs USD</div>
                <div class="fs-4 fw-bold text-danger">
                    {{ $stats['weakest'] ? $stats['weakest']->currency : '—' }}
                </div>
                @if($stats['weakest'])
                    <small class="text-danger fw-semibold">{{ number_format($stats['weakest']->exchange_rate, 4) }}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-white" style="border-top:4px solid #8b5cf6 !important">
            <div class="card-body p-3">
                <div class="text-muted fw-semibold small mb-1">Terakhir Diperbarui</div>
                <div class="fs-5 fw-bold text-secondary">
                    {{ $stats['last_updated'] ? \Carbon\Carbon::parse($stats['last_updated'])->format('d M Y') : '—' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KONTEN FITUR PERBANDINGAN MATA UANG -->
@if($allRates->count() > 0)
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header fw-bold bg-white border-bottom text-dark d-flex align-items-center gap-2 py-3">
        <i class="bi bi-calculator text-primary"></i> Kalkulator Konversi & Perbandingan Mata Uang
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <!-- Kalkulator Konversi -->
            <div class="col-lg-6 border-end pe-lg-4">
                <h5 class="fw-bold text-dark mb-3" style="font-size: 1rem;">🧮 Kalkulator Konversi</h5>
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Jumlah</label>
                    <input type="number" id="calcAmount" class="form-control border" value="1" min="0" step="any" style="border-color:#cbd5e1 !important;">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Dari</label>
                        <select id="calcFrom" class="form-select border" style="border-color:#cbd5e1 !important;">
                            @foreach($allRates as $r)
                                <option value="{{ $r->exchange_rate }}" data-code="{{ $r->currency }}" {{ $r->currency === 'USD' ? 'selected' : '' }}>
                                    {{ $r->currency }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Ke</label>
                        <select id="calcTo" class="form-select border" style="border-color:#cbd5e1 !important;">
                            @foreach($allRates as $r)
                                <option value="{{ $r->exchange_rate }}" data-code="{{ $r->currency }}" {{ $r->currency === 'IDR' ? 'selected' : '' }}>
                                    {{ $r->currency }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Hasil Konversi -->
                <div class="p-3 bg-light rounded-3 text-center border">
                    <div class="text-muted small mb-1 fw-medium">Hasil Konversi</div>
                    <div class="fs-4 fw-extrabold text-dark" id="calcResult" style="font-weight: 800;">
                        1.00 USD = 16,000.00 IDR
                    </div>
                    <div class="text-muted small mt-1" id="calcInverse" style="font-size: 0.75rem;">
                        1 IDR = 0.000062 USD
                    </div>
                </div>
            </div>

            <!-- Perbandingan Kurs Utama -->
            <div class="col-lg-6 ps-lg-4">
                <h5 class="fw-bold text-dark mb-3" style="font-size: 1rem;">📊 Perbandingan Kurs Terhadap Mata Uang Terpilih</h5>
                <p class="text-muted small mb-3">Nilai 1 <strong id="compareBaseCode">USD</strong> jika dikonversikan ke mata uang utama dunia:</p>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle border mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2 small fw-semibold text-muted text-uppercase">Mata Uang</th>
                                <th class="py-2 small fw-semibold text-muted text-uppercase">Nilai Kurs</th>
                                <th class="pe-3 py-2 small fw-semibold text-muted text-uppercase text-end">Simbol</th>
                            </tr>
                        </thead>
                        <tbody id="compareTableBody">
                            <!-- JS will render this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search -->
<div class="card mb-3 border-0 shadow-sm bg-white">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('currency.index') }}" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-color:#cbd5e1;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       style="border-color:#cbd5e1;"
                       placeholder="Cari kode mata uang (contoh: IDR, EUR, JPY)..."
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('currency.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="60">No</th>
                        <th>Kode Mata Uang</th>
                        <th>Kurs (vs 1 USD)</th>
                        <th>Kekuatan</th>
                        <th>Terakhir Update</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rates as $rate)
                    <tr>
                        <td class="ps-4 text-muted">{{ $rates->firstItem() + $loop->index }}</td>

                        <td>
                            <span class="fw-semibold text-dark fs-6">{{ $rate->currency }}</span>
                            @if($rate->currency === 'USD')
                                <span class="badge bg-primary ms-1">Base</span>
                            @endif
                        </td>

                        <td>
                            <span class="fw-semibold text-dark">{{ number_format($rate->exchange_rate, 4) }}</span>
                        </td>

                        <td>
                            @php
                                $val = $rate->exchange_rate;
                                if ($val <= 1) {
                                    $strength = 'Sangat Kuat';
                                    $class = 'badge-risk-low';
                                    $icon = 'arrow-up-circle-fill';
                                } elseif ($val <= 10) {
                                    $strength = 'Kuat';
                                    $class = 'bg-info text-white';
                                    $icon = 'arrow-up-circle';
                                } elseif ($val <= 100) {
                                    $strength = 'Sedang';
                                    $class = 'badge-risk-medium';
                                    $icon = 'dash-circle';
                                } elseif ($val <= 1000) {
                                    $strength = 'Lemah';
                                    $class = 'badge-risk-high';
                                    $icon = 'arrow-down-circle';
                                } else {
                                    $strength = 'Sangat Lemah';
                                    $class = 'bg-danger text-white';
                                    $icon = 'arrow-down-circle-fill';
                                }
                            @endphp
                            <span class="badge {{ $class }}">
                                <i class="bi bi-{{ $icon }} me-1"></i>{{ $strength }}
                            </span>
                        </td>

                        <td class="text-muted small">
                            {{ $rate->updated_at_api ? \Carbon\Carbon::parse($rate->updated_at_api)->format('d M Y') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-currency-exchange display-4 d-block mb-2 opacity-25"></i>
                            Belum ada data exchange rate.
                            <br><small>Klik <strong>"Fetch Exchange Rates"</strong> untuk mengambil data kurs terbaru.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($rates->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">
            Menampilkan {{ $rates->firstItem() }}–{{ $rates->lastItem() }} dari {{ $rates->total() }}
        </small>
        {{ $rates->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calcAmount = document.getElementById('calcAmount');
    const calcFrom = document.getElementById('calcFrom');
    const calcTo = document.getElementById('calcTo');
    const calcResult = document.getElementById('calcResult');
    const calcInverse = document.getElementById('calcInverse');
    const compareBaseCode = document.getElementById('compareBaseCode');
    const compareTableBody = document.getElementById('compareTableBody');

    // Simpan semua rate untuk kalkulasi JS
    const ratesData = {
        @if(isset($allRates))
            @foreach($allRates as $r)
                "{{ $r->currency }}": {{ $r->exchange_rate }},
            @endforeach
        @endif
    };

    const majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'SGD', 'IDR', 'AUD', 'CNY'];

    function calculate() {
        const amount = parseFloat(calcAmount.value) || 0;
        const fromRateUsd = parseFloat(calcFrom.value); // Nilai rate currency A vs USD
        const toRateUsd = parseFloat(calcTo.value);     // Nilai rate currency B vs USD
        
        const fromCode = calcFrom.options[calcFrom.selectedIndex].getAttribute('data-code');
        const toCode = calcTo.options[calcTo.selectedIndex].getAttribute('data-code');

        if (!fromRateUsd || !toRateUsd) return;

        // Formula: A ke B = Amount * (Rate USD ke B / Rate USD ke A)
        const result = amount * (toRateUsd / fromRateUsd);
        const inverse = 1 * (fromRateUsd / toRateUsd);

        // Format angka dengan pemisah ribuan
        const formattedAmount = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(amount);
        const formattedResult = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(result);
        const formattedInverse = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 6 }).format(inverse);

        calcResult.innerHTML = `${formattedAmount} ${fromCode} = ${formattedResult} ${toCode}`;
        calcInverse.innerHTML = `1 ${toCode} = ${formattedInverse} ${fromCode}`;

        // Update Tabel Perbandingan Kurs Utama
        compareBaseCode.innerHTML = fromCode;
        updateCompareTable(fromCode, fromRateUsd);
    }

    function updateCompareTable(baseCode, baseRateUsd) {
        compareTableBody.innerHTML = '';
        
        majorCurrencies.forEach(code => {
            if (ratesData[code] !== undefined) {
                const rateUsd = ratesData[code];
                // Formula: Base ke Target = TargetRateUSD / BaseRateUSD
                const rate = rateUsd / baseRateUsd;
                
                const formattedRate = new Intl.NumberFormat('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 4 
                }).format(rate);

                let symbol = '';
                if (code === 'USD') symbol = '$';
                else if (code === 'EUR') symbol = '€';
                else if (code === 'GBP') symbol = '£';
                else if (code === 'JPY' || code === 'CNY') symbol = '¥';
                else if (code === 'IDR') symbol = 'Rp';
                else if (code === 'SGD' || code === 'AUD') symbol = 'A$';

                const row = `
                    <tr>
                        <td class="ps-3 py-2 fw-semibold text-dark">${code}</td>
                        <td class="py-2 text-dark font-monospace">${formattedRate}</td>
                        <td class="pe-3 py-2 text-muted text-end">${symbol || '—'}</td>
                    </tr>
                `;
                compareTableBody.innerHTML += row;
            }
        });
    }

    // Event Listeners
    if (calcAmount) {
        calcAmount.addEventListener('input', calculate);
        calcFrom.addEventListener('change', calculate);
        calcTo.addEventListener('change', calculate);
        
        // Jalankan kalkulasi pertama kali
        calculate();
    }
});
</script>
@endpush

