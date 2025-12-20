@extends('layouts.app')
@section('title', 'Commande '.$commande->numero_commande)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Commande pharmacie"
        subtitle="{{ $commande->numero_commande }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Commandes', 'href' => route('commandes-pharma.index')],
            ['label' => $commande->numero_commande]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('commandes-pharma.bon', $commande), 'label' => 'Bon PDF', 'icon' => 'file-pdf']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Patient :</strong> {{ $commande->patient?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Pharmacie :</strong> {{ $commande->pharmacie?->nom ?? '-' }}</p>
                            <p class="mb-1"><strong>Mode :</strong> {{ $commande->mode_retrait === 'livraison' ? 'Livraison' : 'Sur place' }}</p>
                            @if($commande->mode_retrait === 'livraison')
                                <p class="mb-1"><strong>Adresse :</strong> {{ $commande->adresse_livraison ?? 'N/A' }}</p>
                                @if($distanceKm)
                                    <p class="mb-1"><strong>Distance :</strong> {{ $distanceKm }} km</p>
                                @endif
                                @if($etaMinutes)
                                    <p class="mb-1"><strong>ETA estimée :</strong> ~{{ $etaMinutes }} min</p>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Montant total :</strong> {{ number_format($commande->montant_total ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$commande->statut ?? 'en_attente'"/></p>
                            <p class="mb-1"><strong>Paiement :</strong> {{ $commande->statut_paiement ?? 'en_attente' }}</p>
                            <p class="mb-1"><strong>Urgent :</strong> {{ $commande->urgent ? 'Oui' : 'Non' }}</p>
                            <p class="mb-0"><strong>Date :</strong> {{ optional($commande->date_commande ?? $commande->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Instructions :</strong>
                        <div class="text-muted">{{ $commande->instructions_speciales ?? 'Aucune' }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Qté</th>
                                    <th>PU</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commande->lignes as $ligne)
                                    <tr>
                                        <td>{{ $ligne->produitPharmaceutique?->nom_commercial ?? '-' }}</td>
                                        <td>{{ $ligne->quantite_commandee }}</td>
                                        <td>{{ number_format($ligne->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($ligne->montant_ligne ?? 0, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if($commande->mode_retrait === 'livraison')
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-route me-2"></i>Accès et logistique</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary"><i class="fas fa-map-pin me-1"></i> Trajet</span>
                                <span class="fw-semibold">
                                    @if($distanceKm)
                                        {{ $distanceKm }} km @if($etaMinutes) • ~{{ $etaMinutes }} min @endif
                                    @else
                                        Coordonnées manquantes pour estimer le trajet.
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="fas fa-motorcycle text-primary"></i>
                                <small>Estimation sur base d’un trajet urbain mix moto/voiture.</small>
                            </div>
                        </div>
                        @if($horsZone)
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-triangle-exclamation"></i>
                                <div>
                                    Adresse hors rayon de livraison déclaré ({{ $commande->pharmacie->rayon_livraison_km }} km).
                                    Proposez un retrait, un transport alternatif ou une téléconsultation.
                                </div>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="fw-semibold mb-1"><i class="fas fa-car-side me-2 text-primary"></i>Options transport</div>
                                    <ul class="mb-0 text-muted small">
                                        <li>Transport rapide (moto/voiture) si disponible</li>
                                        <li>Coordonner le livreur avec l’adresse précise</li>
                                        <li>Retrait sur place si plus simple</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="fw-semibold mb-1"><i class="fas fa-video me-2 text-primary"></i>Option téléconsultation</div>
                                    <p class="text-muted small mb-0">
                                        Si l’adresse est trop éloignée ou en zone difficile, proposez une téléconsultation pour valider l’ordonnance avant expédition ou orienter vers une pharmacie plus proche.
                                    </p>
                                    <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary btn-sm mt-2">
                                        Ouvrir la téléconsultation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    @if($commande->statut === 'en_attente')
                        <form method="POST" action="{{ route('commandes-pharma.confirmer', $commande) }}">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit"><i class="fas fa-check me-1"></i>Confirmer</button>
                        </form>
                    @endif
                    @if($commande->statut === 'confirmee')
                        <form method="POST" action="{{ route('commandes-pharma.preparer', $commande) }}">@csrf
                            <button class="btn btn-secondary w-100" type="submit"><i class="fas fa-box-open me-1"></i>Préparer</button>
                        </form>
                    @endif
                    @if(in_array($commande->statut, ['confirmee','en_preparation']))
                        <form method="POST" action="{{ route('commandes-pharma.prete', $commande) }}">@csrf
                            <button class="btn btn-success w-100" type="submit"><i class="fas fa-check-circle me-1"></i>Marquer prête</button>
                        </form>
                    @endif
                    @if($commande->statut === 'prete' && $commande->mode_retrait === 'livraison')
                        <form method="POST" action="{{ route('commandes-pharma.livraison', $commande) }}">@csrf
                            <div class="mb-2">
                                <x-lobiko.forms.input name="livreur_id" label="Livreur (UUID)" required />
                            </div>
                            <button class="btn btn-info w-100" type="submit"><i class="fas fa-truck me-1"></i>Démarrer livraison</button>
                        </form>
                    @endif
                    @if(in_array($commande->statut, ['prete','en_livraison']))
                        <form method="POST" action="{{ route('commandes-pharma.livree', $commande) }}" enctype="multipart/form-data">@csrf
                            <x-lobiko.forms.input name="nom_receptionnaire" label="Nom réceptionnaire" required />
                            <x-lobiko.forms.input name="telephone_receptionnaire" label="Téléphone" required />
                            <button class="btn btn-outline-success w-100 mt-2" type="submit"><i class="fas fa-flag-checkered me-1"></i>Confirmer livraison</button>
                        </form>
                    @endif
                    @if(!in_array($commande->statut, ['livree','annulee']))
                        <form method="POST" action="{{ route('commandes-pharma.annuler', $commande) }}">@csrf
                            <x-lobiko.forms.input name="motif" label="Motif annulation" required />
                            <button class="btn btn-outline-danger w-100 mt-2" type="submit"><i class="fas fa-ban me-1"></i>Annuler</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span>Code de retrait</span>
                    <span class="fw-bold">{{ $commande->code_retrait }}</span>
                </div>
                <div class="card-body text-center">
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" class="img-fluid" style="max-width:200px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
