@extends('layouts.app')
@section('title','Paiements')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Paiements"
        subtitle="Historique des transactions"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        @foreach(\App\Models\Paiement::STATUTS as $statut)
                            <option value="{{ $statut }}" {{ request('statut') === $statut ? 'selected' : '' }}>{{ ucfirst($statut) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <x-lobiko.forms.input name="reference" label="Référence paiement" :value="request('reference')" />
                </div>
                <div class="col-md-3">
                    <x-lobiko.forms.input name="mode_paiement" label="Mode" :value="request('mode_paiement')" />
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <x-lobiko.buttons.primary type="submit" icon="fas fa-filter">Filtrer</x-lobiko.buttons.primary>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Référence</th>
                    <th>Statut</th>
                    <th>Montant</th>
                    <th>Payeur</th>
                    <th>Mode</th>
                    <th>Date</th>
                </x-slot>
                @forelse($payments as $pay)
                    <tr>
                        <td class="fw-semibold">{{ $pay->numero_paiement ?? $pay->id }}</td>
                        <td><x-lobiko.ui.badge-status :status="$pay->statut" /></td>
                        <td>{{ number_format($pay->montant ?? 0, 2, ',', ' ') }} {{ $pay->devise ?? 'CFA' }}</td>
                        <td>{{ $pay->payeur?->name ?? '-' }}</td>
                        <td>{{ $pay->mode_paiement ?? '-' }}</td>
                        <td>{{ optional($pay->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-lobiko.ui.empty-state
                                title="Aucun paiement"
                                description="Aucune transaction à afficher."
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->withQueryString()->links() }}
    </div>
</div>
@endsection
