@extends('layouts.app')

@section('title', 'Salle de teleconsultation')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Salle virtuelle</h5>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <p class="text-muted mb-0">
                            Session : {{ $session->room_name }} • Statut : {{ ucfirst($session->status) }}
                        </p>
                        <span class="badge bg-{{ $session->status === 'live' ? 'success' : ($session->status === 'ended' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                    <div class="alert alert-info">
                        <div class="d-flex flex-column gap-1">
                            <div><strong>Fournisseur :</strong> {{ $provider['provider'] ?? $session->provider ?? 'Non defini' }}</div>
                            <div><strong>Lien securise :</strong>
                                @if(!empty($provider['join_url']))
                                    <span class="text-success">Pret</span>
                                @else
                                    <span class="text-muted">Non disponible</span>
                                @endif
                            </div>
                            <div class="text-muted small">Le jeton reste masque pour limiter les fuites. Utilisez le bouton "Rejoindre".</div>
                            @if(!empty($session->token_expires_at))
                                <div class="small"><strong>Expiration du lien :</strong> {{ $session->token_expires_at }}</div>
                            @endif
                        </div>
                    </div>
                    @if($session->status === 'pending')
                        <div class="alert alert-warning mb-3">
                            Salle d'attente : le professionnel n'a pas encore rejoint. Vous serez connecte des que possible.
                        </div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-gradient"
                                id="joinSession"
                                data-join-enc="{{ !empty($provider['join_url']) ? base64_encode($provider['join_url']) : '' }}"
                                data-expiry="{{ optional($session->token_expires_at)->timestamp ?? '' }}"
                                data-status="{{ $session->status }}">
                            <i class="fas fa-video me-2"></i> Rejoindre
                        </button>
                        <button class="btn btn-outline-danger" id="endSession">
                            <i class="fas fa-phone-slash me-2"></i> Terminer
                        </button>
                        <span class="text-muted small" id="expiryTimer"></span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Chat securise</h6>
                    <div class="border rounded p-3 mb-3" style="height: 240px; overflow-y: auto;">
                        @forelse($session->messages as $message)
                            <div class="mb-2">
                                <small class="text-muted">
                                    {{ optional($message->sender)->prenom ?? 'Systeme' }}
                                    • {{ $message->created_at->format('H:i') }}
                                </small>
                                <div>{{ $message->body }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucun message pour le moment.</p>
                        @endforelse
                    </div>
                    <form method="POST" action="{{ route('teleconsultation.message', $consultation) }}" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="message" class="form-control" placeholder="Votre message" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">Documents partages</h6>
                    <p class="text-muted small mb-2">Formats autorises : PDF/JPG/PNG, 5 Mo max, jusqu a 20 documents.</p>
                    <div class="border rounded p-3 mb-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($fileLinks as $entry)
                            @php
                                $file = $entry['file'];
                                $url = $entry['url'];
                            @endphp
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $file->original_name }}</div>
                                    <small class="text-muted">
                                        {{ $file->mime_type }} • {{ number_format(($file->size ?? 0)/1024, 1) }} Ko
                                    </small>
                                </div>
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    Ouvrir
                                </a>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucun fichier partage.</p>
                        @endforelse
                    </div>
                    <form method="POST"
                          action="{{ route('teleconsultation.file', $consultation) }}"
                          enctype="multipart/form-data"
                          class="d-flex gap-2"
                          id="fileUploadForm"
                          data-remaining="{{ max(0, 20 - count($fileLinks)) }}"
                          data-max-size="5120">
                        @csrf
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-upload me-1"></i> Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Consultation</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>Patient :</strong> {{ $consultation->patient->prenom ?? 'N/A' }}</li>
                        <li class="mb-2"><strong>Professionnel :</strong> {{ $consultation->professionnel->prenom ?? 'N/A' }}</li>
                        <li class="mb-2"><strong>Date :</strong> {{ $consultation->date_consultation }}</li>
                        <li><strong>Modalite :</strong> {{ ucfirst($consultation->modalite) }}</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Aide</h6>
                    <p class="text-muted mb-2">Assurez-vous d'avoir une connexion stable et un navigateur a jour.</p>
                    <p class="text-muted mb-0">Pour partager un document, utilisez le bouton prevu. Ne partagez pas de donnees sensibles sans consentement.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const joinBtn = document.getElementById('joinSession');
        const expiryEl = document.getElementById('expiryTimer');
        const fileForm = document.getElementById('fileUploadForm');
        const fileInput = fileForm ? fileForm.querySelector('input[type="file"]') : null;
        if (!joinBtn) return;

        const decodeUrl = () => {
            const enc = joinBtn.getAttribute('data-join-enc') || '';
            if (!enc) return '';
            try { return atob(enc); } catch (e) { return ''; }
        };

        const expiryTs = parseInt(joinBtn.getAttribute('data-expiry') || '', 10);
        const status = joinBtn.getAttribute('data-status') || '';

        const renderCountdown = () => {
            if (!expiryTs || !expiryEl) return;
            const now = Date.now();
            const diff = (expiryTs * 1000) - now;
            if (diff <= 0) {
                expiryEl.textContent = 'Lien expire, demandez un nouvel acces.';
                return;
            }
            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            expiryEl.textContent = 'Expire dans ' + mins + 'm ' + secs + 's';
        };

        if (expiryTs) {
            renderCountdown();
            setInterval(renderCountdown, 1000);
        }

        joinBtn.addEventListener('click', function () {
            const joinUrl = decodeUrl();
            if (!joinUrl) {
                alert('Le lien de salle n est pas disponible. Veuillez patienter ou recharger.');
                return;
            }
            if (status === 'ended') {
                alert('Session terminee. Contactez le support pour rouvrir une salle.');
                return;
            }
            if (expiryTs && Date.now() > (expiryTs * 1000)) {
                alert('Le lien a expire. Merci de demander un nouveau lien.');
                return;
            }
            window.open(joinUrl, '_blank', 'noopener');
        });

        // Pre-check upload (nombre et taille)
        if (fileForm && fileInput) {
            const remaining = parseInt(fileForm.getAttribute('data-remaining') || '0', 10);
            const maxSizeKb = parseInt(fileForm.getAttribute('data-max-size') || '0', 10);

            if (remaining <= 0) {
                fileInput.disabled = true;
            }

            fileForm.addEventListener('submit', function (e) {
                if (remaining <= 0) {
                    e.preventDefault();
                    alert('Limite de documents atteinte pour cette teleconsultation.');
                    return;
                }
                const file = fileInput.files[0];
                if (!file) return;
                const sizeKb = file.size / 1024;
                if (maxSizeKb && sizeKb > maxSizeKb) {
                    e.preventDefault();
                    alert('Fichier trop volumineux (max 5 Mo).');
                }
            });
        }
    });
</script>
@endpush
