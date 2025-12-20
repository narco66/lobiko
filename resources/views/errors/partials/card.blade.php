@php
    $code = $code ?? 500;
    $title = $title ?? 'Une erreur est survenue';
    $message = $message ?? "Une erreur inattendue s'est produite. Veuillez réessayer.";
    $accent = [
        'bg' => [
            403 => 'rgba(255, 214, 102, 0.18)',
            404 => 'rgba(102, 126, 234, 0.18)',
            419 => 'rgba(244, 114, 182, 0.18)',
            500 => 'rgba(248, 113, 113, 0.18)',
        ][$code] ?? 'rgba(102, 126, 234, 0.18)',
        'icon' => [
            403 => 'fa-lock',
            404 => 'fa-location-dot',
            419 => 'fa-rotate',
            500 => 'fa-circle-exclamation',
        ][$code] ?? 'fa-circle-exclamation',
    ];
@endphp

@push('styles')
<style>
    .error-hero {
        background: linear-gradient(135deg, #f8f9ff 0%, #f4f6ff 100%);
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
    }
    .error-card {
        border: 1px solid #e5e7f3;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        padding: 28px;
        background: #fff;
    }
    .error-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 999px;
        background: {{ $accent['bg'] }};
        font-weight: 700;
        color: #0f172a;
    }
    .error-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        box-shadow: 0 18px 40px rgba(102, 126, 234, 0.3);
    }
    .error-actions .btn {
        border-radius: 12px;
    }
</style>
@endpush

<div class="error-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="error-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="error-icon">
                            <i class="fas {{ $accent['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="error-badge">Erreur {{ $code }}</div>
                            <h1 class="h3 fw-bold mt-2 mb-1">{{ $title }}</h1>
                            <p class="text-muted mb-0">{{ $message }}</p>
                        </div>
                    </div>
                    <div class="error-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-house me-2"></i>Retour à l'accueil
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Page précédente
                        </button>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Connexion
                            </a>
                        @endguest
                        <a href="{{ route('contact') }}" class="btn btn-link text-decoration-none">
                            Besoin d'aide ?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
