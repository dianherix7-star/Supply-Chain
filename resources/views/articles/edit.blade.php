@extends('layouts.app')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Artikel</div>
            <div class="card-body">
                <form action="{{ route('articles.update', $article) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penulis</label>
                        <input type="text" name="author" class="form-control" value="{{ old('author', $article->author) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konten</label>
                        <textarea name="content" rows="12" class="form-control" required>{{ old('content', $article->content) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update</button>
                        <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
