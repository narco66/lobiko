@extends('layouts.app')
@section('title','Paiements')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Paiements" subtitle="Historique des transactions" />
    <x-lobiko.ui.flash />

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <x-lobiko.forms.select name="statut" label="Statut" :options="[''=>'Tous','pending'=>'En attente','confirmed'=>'Confirmé','failed'=>'Échec']" :selected="request('statut')" />
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <x-lobiko.buttons.primary type="submit">Filtrer</x-lobiko.buttons.primary>
        </div>
    </form>

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Référence</th>
            <th>Statut</th>
            <th>Montant</th>
            <th>Payeur</th>
            <th>Facture</th>
            <th>Date</th>
        </x-slot>
        @forelse($payments as $pay)
            <tr>
                <td>{{ $pay->reference ?? $pay->id }}</td>
                <td><x-lobiko.ui.badge-status :status="$pay->statut" /></td>
                <td>{{ number_format($pay->montant, 2, ',', ' ') }} CFA</td>
                <td>{{ $pay->payeur_id }}</td>
                <td>{{ $pay->facture_id }}</td>
                <td>{{ $pay->created_at }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">Aucun paiement</td></tr>
        @endforelse
    </x-lobiko.tables.datatable>

    {{ $payments->withQueryString()->links() }}
</div>
@endsection
