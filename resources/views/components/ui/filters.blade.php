<div class="card mb-3">
    <div class="card-body">
        <form method="get" {{ $attributes }}>
            <div class="row g-2 align-items-end">
                {{ $slot }}
                <div class="col-auto">
                    <x-ui.button type="submit" variant="primary" icon="fas fa-search">Rechercher</x-ui.button>
                </div>
                <div class="col-auto">
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>
