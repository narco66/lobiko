@props(['title' => 'Aucun résultat', 'description' => null, 'action' => null])

<div class="text-center p-4 border rounded bg-light">
    <i class="fas fa-inbox fa-2x text-muted mb-2" aria-hidden="true"></i>
    <h5 class="mb-1">{{ $title }}</h5>
    @if($description)<p class="text-muted mb-2">{{ $description }}</p>@endif
    @if($action)
        <x-ui.button :href="$action['href'] ?? '#'" :icon="$action['icon'] ?? null">
            {{ $action['label'] ?? 'Créer' }}
        </x-ui.button>
    @endif
</div>
