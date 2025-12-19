@props(['href' => null, 'icon' => null, 'type' => 'button', 'disabled' => false])

<x-ui.button :href="$href" :icon="$icon" :type="$type" :disabled="$disabled" variant="danger" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
