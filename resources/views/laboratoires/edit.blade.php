@extends('layouts.app')

@section('title', 'Modifier laboratoire')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <p class="text-uppercase text-primary small fw-semibold mb-1">Laboratoires</p>
            <h1 class="h4 fw-bold mb-0">Modifier {{ $laboratoire->nom }}</h1>
        </div>
        <a href="{{ route('admin.laboratoires.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.laboratoires.update', $laboratoire) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="{{ old('nom', $laboratoire->nom) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="responsable" class="form-control" value="{{ old('responsable', $laboratoire->responsable) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $laboratoire->telephone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $laboratoire->email) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" id="adresse" class="form-control" value="{{ old('adresse', $laboratoire->adresse) }}" placeholder="Ex: Carrefour Léon Mba, Libreville">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ville</label>
                    <input type="text" name="ville" class="form-control" value="{{ old('ville', $laboratoire->ville) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pays</label>
                    <input type="text" name="pays" class="form-control" value="{{ old('pays', $laboratoire->pays) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="0.000001" name="latitude" id="latitude" class="form-control" value="{{ old('latitude', $laboratoire->latitude) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="0.000001" name="longitude" id="longitude" class="form-control" value="{{ old('longitude', $laboratoire->longitude) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Localisation sur carte</label>
                    <div id="map" style="height: 280px; border-radius: 12px; border: 1px solid #e5e7eb;"></div>
                    <small class="text-muted">Déplacez le marqueur ou recherchez une adresse, les coordonnées se mettent à jour automatiquement.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rayon de couverture (km)</label>
                    <input type="number" step="0.1" name="rayon_couverture_km" class="form-control" value="{{ old('rayon_couverture_km', $laboratoire->rayon_couverture_km) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        @foreach(['actif'=>'Actif','maintenance'=>'En maintenance','suspendu'=>'Suspendu'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('statut', $laboratoire->statut) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.laboratoires.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(config('services.google.maps_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
    <script>
        (function() {
            let map, marker, geocoder, autocomplete;
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const addressInput = document.getElementById('adresse');

            function initMap() {
                const defaultPos = {
                    lat: parseFloat(latInput.value) || 0.3901,
                    lng: parseFloat(lngInput.value) || 9.4544
                };
                map = new google.maps.Map(document.getElementById('map'), {
                    center: defaultPos,
                    zoom: 13,
                    mapTypeControl: false,
                });
                geocoder = new google.maps.Geocoder();
                marker = new google.maps.Marker({
                    position: defaultPos,
                    map,
                    draggable: true,
                });
                marker.addListener('dragend', () => {
                    const pos = marker.getPosition();
                    updateLatLng(pos);
                });
                autocomplete = new google.maps.places.Autocomplete(addressInput);
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;
                    const loc = place.geometry.location;
                    map.panTo(loc);
                    marker.setPosition(loc);
                    updateLatLng(loc);
                });
            }

            function updateLatLng(position) {
                latInput.value = position.lat().toFixed(6);
                lngInput.value = position.lng().toFixed(6);
            }

            document.addEventListener('DOMContentLoaded', initMap);
        })();
    </script>
@else
    <script>console.warn('Clé Google Maps manquante (services.google.maps_key)');</script>
@endif
@endpush
