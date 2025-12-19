@extends('layouts.app')
@section('title','Catégories')
@section('content')
<x-lobiko.page-header title="Catégories" :actions="[
    ['label'=>'Créer','url'=>route('admin.blog.categories.create'),'type'=>'primary','can'=>'create,App\\Models\\ArticleCategory']
]" />
<x-lobiko.ui.flash />

@if($categories->count())
    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Nom</th>
            <th>Parent</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->parent?->name ?? '—' }}</td>
                <td class="text-end">
                    @can('update',$category)
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.blog.categories.edit',$category) }}">Éditer</a>
                    @endcan
                    @can('delete',$category)
                        <form action="{{ route('admin.blog.categories.destroy',$category) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>
    {{ $categories->links() }}
@else
    <x-lobiko.ui.empty-state message="Aucune catégorie" />
@endif
@endsection
