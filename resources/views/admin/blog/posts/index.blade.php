@extends('layouts.app')

@section('title', 'Articles')

@section('content')
<x-lobiko.page-header title="Articles" :actions="[
    ['label' => 'Créer', 'url' => route('admin.blog.posts.create'), 'type' => 'primary', 'can' => 'create,App\\Models\\Article'],
]" />

<x-lobiko.ui.flash />

<form method="get" class="mb-3 d-flex gap-2">
    <input type="text" name="q" class="form-control" placeholder="Recherche..." value="{{ request('q') }}">
    <select name="status" class="form-select">
        <option value="">Tous statuts</option>
        @foreach(['draft'=>'Brouillon','review'=>'Relecture','published'=>'Publié','archived'=>'Archivé'] as $key=>$label)
            <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="category_id" class="form-select">
        <option value="">Toutes catégories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <button class="btn btn-outline-secondary">Filtrer</button>
</form>

@if($posts->count())
    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Titre</th>
            <th>Statut</th>
            <th>Catégorie</th>
            <th>Auteur</th>
            <th>Publié le</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td><x-lobiko.ui.badge-status :status="$post->status" /></td>
                <td>{{ $post->category?->name ?? '—' }}</td>
                <td>{{ $post->author?->name ?? '—' }}</td>
                <td>{{ optional($post->published_at)->format('d/m/Y H:i') ?? '—' }}</td>
                <td class="text-end">
                    @can('publish', $post)
                        @if($post->status !== 'published')
                            <form action="{{ route('admin.blog.posts.publish', $post) }}" method="post" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success" onclick="return confirm('Publier cet article ?')">Publier</button>
                            </form>
                        @else
                            <form action="{{ route('admin.blog.posts.unpublish', $post) }}" method="post" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning" onclick="return confirm('Dépublier cet article ?')">Dépublier</button>
                            </form>
                        @endif
                    @endcan
                    @can('update', $post)
                        <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Éditer</a>
                    @endcan
                    @can('delete', $post)
                        <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>

    {{ $posts->withQueryString()->links() }}
@else
    <x-lobiko.ui.empty-state message="Aucun article" />
@endif
@endsection
