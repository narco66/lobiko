@extends('layouts.app')

@section('title', 'Laboratoires')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <p class="text-uppercase text-primary small fw-semibold mb-1">Laboratoires</p>
            <h1 class="h4 fw-bold mb-0">Gestion des laboratoires</h1>
        </div>
        <a href="{{ route('admin.laboratoires.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i>Nouveau laboratoire
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Ville</th>
                            <th>Couverture</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($labs as $lab)
                            <tr>
                                <td class="fw-semibold">{{ $lab->nom }}</td>
                                <td>{{ $lab->responsable ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $lab->statut === 'actif' ? 'success' : ($lab->statut === 'maintenance' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($lab->statut) }}
                                    </span>
                                </td>
                                <td>{{ $lab->ville ?? '—' }}</td>
                                <td>{{ $lab->rayon_couverture_km ? $lab->rayon_couverture_km.' km' : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('laboratoires.edit', $lab) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('laboratoires.destroy', $lab) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce laboratoire ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun laboratoire pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $labs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
