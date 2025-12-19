@extends('layouts.app')

@section('title', 'FAQ LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="h3 fw-bold">FAQ LOBIKO</h1>
            <p class="text-muted mb-0">Questions fréquentes sur les comptes, rendez-vous, e-pharmacie, paiements, livraisons et assurances.</p>
        </div>
        <div class="col-lg-4 d-flex align-items-center">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input id="faq-search" type="text" class="form-control" placeholder="Rechercher une question...">
            </div>
        </div>
    </div>

    <div class="accordion" id="faqAccordion">
        @foreach($faqs as $sectionIndex => $section)
            <div class="mb-4" id="section-{{ Str::slug($section['section']) }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h5 mb-0">{{ $section['section'] }}</h2>
                    <a href="#section-{{ Str::slug($section['section']) }}" class="text-decoration-none text-muted small">#</a>
                </div>
                <div class="accordion" id="accordion-{{ $sectionIndex }}">
                    @foreach($section['items'] as $itemIndex => $item)
                        @php $id = "faq-{$sectionIndex}-{$itemIndex}"; @endphp
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="heading-{{ $id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $id }}" aria-expanded="false" aria-controls="collapse-{{ $id }}">
                                    {{ $item['q'] }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $id }}" data-bs-parent="#accordion-{{ $sectionIndex }}">
                                <div class="accordion-body text-muted">
                                    {{ $item['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h3 class="h5 mb-1">Besoin d'aide supplémentaire ?</h3>
                <p class="text-muted mb-0">Contactez notre support, précisez votre numéro de commande ou rendez-vous.</p>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-primary mt-3 mt-md-0">
                <i class="fas fa-headset me-2"></i> Contacter le support
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('faq-search').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.faq-item').forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
