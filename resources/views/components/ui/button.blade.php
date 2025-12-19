@props([
    'variant' => 'primary', // primary, secondary, danger, link
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'disabled' => false,
])

@php
    $classes = match($variant) {
        'primary' => 'btn btn-primary',
        'secondary' => 'btn btn-outline-secondary',
        'danger' => 'btn btn-outline-danger',
        'link' => 'btn btn-link text-decoration-none',
        default => 'btn btn-primary',
    };
    $classes .= $disabled ? ' disabled' : '';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-label' => $attributes->get('title')]) }}>
        @if($icon)<i class="{{ $icon }} me-1" aria-hidden="true"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes, 'aria-label' => $attributes->get('title'), 'aria-disabled' => $disabled]) }} @if($disabled) disabled @endif>
        @if($icon)<i class="{{ $icon }} me-1" aria-hidden="true"></i>@endif
        {{ $slot }}
    </button>
@endif
