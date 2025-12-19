@props(['href' => null, 'icon' => null, 'type' => 'button', 'disabled' => false])

<x-ui.button :href="$href" :icon="$icon" :type="$type" :disabled="$disabled" variant="primary" {{ $attributes }}>
    {{ $slot }}
</x-ui.button>
