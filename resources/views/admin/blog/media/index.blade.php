@extends('layouts.app')
@section('title','Médias')
@section('content')
<x-lobiko.page-header title="Bibliothèque médias" />
<x-lobiko.ui.flash />

<div class="card p-3 mb-3">
    <form action="{{ route('admin.blog.media.store') }}" method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
        @csrf
        <input type="file" name="file" class="form-control" required>
        <button class="btn btn-primary">Uploader</button>
    </form>
</div>

@if($media->count())
    <div class="row g-3">
        @foreach($media as $file)
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="small text-muted">{{ $file->mime }} • {{ number_format($file->size/1024,1) }} Ko</div>
                        <div class="fw-semibold mt-1">{{ $file->original_name }}</div>
                        <div class="text-muted small">{{ $file->alt_text }}</div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <span class="small">{{ optional($file->uploader)->name }}</span>
                        @can('delete',$file)
                            <form action="{{ route('admin.blog.media.destroy',$file) }}" method="post">
                                @csrf @method('delete')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Suppr.</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3">
        {{ $media->links() }}
    </div>
@else
    <x-lobiko.ui.empty-state message="Aucun média" />
@endif
@endsection
