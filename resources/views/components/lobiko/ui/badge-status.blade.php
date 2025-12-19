<x-ui.badge-status {{ $attributes }} :status="$status ?? ($attributes->get('status') ?? 'info')">
    {{ $slot }}
</x-ui.badge-status>
