@extends('layouts.app')

@section('title', $article->title)
@section('page-title', 'Baca Artikel')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="mb-3">
            <a href="{{ route('articles.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card">
            <div class="card-body p-4">
                <h1 class="fw-bold mb-2" style="font-size:1.6rem;">{{ $article->title }}</h1>
                <div class="text-muted small mb-4">
                    <i class="bi bi-person me-1"></i>{{ $article->author }}
                    <span class="ms-3"><i class="bi bi-calendar me-1"></i>{{ $article->created_at->format('d M Y') }}</span>
                </div>
                <div style="line-height:1.8;white-space:pre-wrap;">{{ $article->content }}</div>
                <hr class="mt-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
