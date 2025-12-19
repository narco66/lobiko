@extends('layouts.app')
@section('title','Tags')
@section('content')
<x-lobiko.page-header title="Tags" :actions="[
    ['label'=>'Créer','url'=>route('admin.blog.tags.create'),'type'=>'primary','can'=>'create,App\\Models\\BlogTag']
]" />
<x-lobiko.ui.flash />

@if($tags->count())
    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Nom</th>
            <th>Slug</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->slug }}</td>
                <td class="text-end">
                    @can('update',$tag)
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.blog.tags.edit',$tag) }}">Éditer</a>
                    @endcan
                    @can('delete',$tag)
                        <form action="{{ route('admin.blog.tags.destroy',$tag) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>
    {{ $tags->links() }}
@else
    <x-lobiko.ui.empty-state message="Aucun tag" />
@endif
@endsection
