@extends('layouts.app')

@section('title', 'Articles')
@section('page-title', 'Artikel Analisis')

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="section-title">📝 Artikel Analisis</h1>
        <p class="section-sub">Kelola artikel analisis supply chain dan risiko global</p>
    </div>
    <a href="{{ route('articles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tulis Artikel Baru
    </a>
</div>

@if($articles->count() > 0)
<div class="row g-3">
    @foreach($articles as $article)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-1">{{ $article->title }}</h5>
                <div class="text-muted small mb-2">
                    <i class="bi bi-person me-1"></i>{{ $article->author }}
                    <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $article->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-muted small mb-3">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('articles.show', $article) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> Baca
                    </a>
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Hapus artikel ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-3">{{ $articles->links() }}</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-file-earmark-text display-2 d-block mb-3 opacity-25"></i>
        <h5 class="text-muted">Belum ada artikel</h5>
        <a href="{{ route('articles.create') }}" class="btn btn-primary mt-2">
            <i class="bi bi-plus-lg me-1"></i> Tulis Artikel Pertama
        </a>
    </div>
</div>
@endif

@endsection
