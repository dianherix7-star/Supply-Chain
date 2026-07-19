@extends('layouts.app')

@section('page-title', 'News & Sentiment')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">📰 News Intelligence & Sentiment Analysis</h4>
        <small class="text-muted">Berita supply chain, ekonomi, logistik, dan geopolitik per negara</small>
        <div class="mt-1 d-flex gap-2 flex-wrap">
            <span class="badge" style="background:#16a34a;font-size:0.7rem;">
                <i class="bi bi-rss me-1"></i>GNews API — Aktif ✓
            </span>
            @if($stats['total'] > 0)
            <span class="badge bg-success" style="font-size:0.7rem;">Positive: {{ $stats['pos_pct'] }}%</span>
            <span class="badge bg-secondary" style="font-size:0.7rem;">Neutral: {{ $stats['neu_pct'] }}%</span>
            <span class="badge bg-danger" style="font-size:0.7rem;">Negative: {{ $stats['neg_pct'] }}%</span>
            @endif
        </div>
    </div>
    @if(Auth::user()->isAdmin())
    <div class="d-flex gap-2">
        <!-- Fetch Semua (10 Negara Utama) -->
        <form action="{{ route('news.fetch-all') }}" method="POST"
              onsubmit="this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Mengambil...';this.querySelector('button').disabled=true;">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-download me-1"></i> Fetch Global (GNews)
            </button>
        </form>
    </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #0d6efd">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small"><i class="bi bi-newspaper me-1"></i>Total Berita</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #198754">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['positive'] }}</div>
                <div class="text-muted small"><i class="bi bi-emoji-smile me-1"></i>Positif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #6c757d">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-secondary">{{ $stats['neutral'] }}</div>
                <div class="text-muted small"><i class="bi bi-emoji-neutral me-1"></i>Netral</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="border-top:3px solid #dc3545">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger">{{ $stats['negative'] }}</div>
                <div class="text-muted small"><i class="bi bi-emoji-frown me-1"></i>Negatif</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('news.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Cari judul berita..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="country_id" class="form-select">
                    <option value="">Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="sentiment" class="form-select">
                    <option value="">Semua Sentiment</option>
                    <option value="Positive" {{ request('sentiment') == 'Positive' ? 'selected' : '' }}>🟢 Positive</option>
                    <option value="Neutral" {{ request('sentiment') == 'Neutral' ? 'selected' : '' }}>⚪ Neutral</option>
                    <option value="Negative" {{ request('sentiment') == 'Negative' ? 'selected' : '' }}>🔴 Negative</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            @if(request('search') || request('sentiment') || request('country_id'))
                    <a href="{{ route('news.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Fetch Berita per Negara -->
<div class="card mb-3" style="border-left: 4px solid #0d6efd;">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <div class="fw-semibold small">
                    <i class="bi bi-rss text-primary me-1"></i>
                    Fetch Berita per Negara via <strong>GNews API</strong>
                </div>
                <small class="text-muted">
                    Keyword: nama negara + supply chain OR economy OR trade OR logistics
                    · Sentiment dianalisis otomatis (Lexicon-Based)
                </small>
            </div>
            <form action="#" method="POST" id="fetchNewsCountryForm">
                @csrf
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-select form-select-sm" id="newsCountrySelect" style="min-width:200px;" required>
                        <option value="">Pilih Negara...</option>
                        @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary" id="fetchNewsBtn">
                        <i class="bi bi-cloud-download me-1"></i> Fetch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


<!-- News Cards -->
<div class="row g-3">
    @forelse($news as $item)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        @if($item->country)
                            <span class="badge bg-light text-dark border me-1">
                                @if($item->country->flag_url)
                                    <img src="{{ $item->country->flag_url }}" width="16" height="11"
                                         class="me-1" style="border-radius:2px;"
                                         alt="{{ $item->country->country_name }}"
                                         loading="lazy">
                                @endif
                                {{ $item->country->country_name }}
                            </span>
                        @endif
                        @if($item->source)
                            <span class="badge bg-light text-muted border">{{ $item->source }}</span>
                        @endif
                    </div>
                    @php
                        $sentClass = match($item->sentiment) {
                            'Positive' => 'success',
                            'Negative' => 'danger',
                            default => 'secondary',
                        };
                        $sentIcon = match($item->sentiment) {
                            'Positive' => 'emoji-smile-fill',
                            'Negative' => 'emoji-frown-fill',
                            default => 'emoji-neutral-fill',
                        };
                    @endphp
                    <span class="badge bg-{{ $sentClass }}">
                        <i class="bi bi-{{ $sentIcon }} me-1"></i>{{ $item->sentiment ?? 'Neutral' }}
                    </span>
                </div>

                <h6 class="fw-semibold mb-2">
                    @if($item->url)
                        <a href="{{ $item->url }}" target="_blank" class="text-dark text-decoration-none">
                            {{ Str::limit($item->title, 100) }}
                            <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.7rem;"></i>
                        </a>
                    @else
                        {{ Str::limit($item->title, 100) }}
                    @endif
                </h6>

                @if($item->description)
                    <p class="text-muted small mb-0">{{ Str::limit($item->description, 150) }}</p>
                @endif
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : '—' }}
                </small>
            </div>
        </div>
    </div>

    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-newspaper display-4 d-block mb-2 opacity-25"></i>
                Belum ada data berita.
                <br><small>Klik <strong>"Fetch Berita"</strong> untuk mengambil berita terkait supply chain.</small>
                <br><small class="text-muted">Pastikan <code>NEWS_API_KEY</code> sudah diset di file <code>.env</code></small>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($news->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">
        Menampilkan {{ $news->firstItem() }}–{{ $news->lastItem() }} dari {{ $news->total() }}
    </small>
    {{ $news->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
// Handler: Fetch berita per negara dari GNews API
const fetchNewsForm = document.getElementById('fetchNewsCountryForm');
if (fetchNewsForm) {
    fetchNewsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const countryId = document.getElementById('newsCountrySelect').value;
        if (!countryId) { alert('Pilih negara terlebih dahulu.'); return; }

        const btn = document.getElementById('fetchNewsBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengambil...';
        btn.disabled = true;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/news/fetch/${countryId}`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>
@endpush
