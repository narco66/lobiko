@props(['label', 'value' => 0, 'icon' => null, 'variant' => 'primary'])
@php
    $bg = match($variant) {
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'success' => 'bg-success',
        'info' => 'bg-info',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        default => 'bg-primary',
    };
@endphp
<div class="card text-white {{ $bg }} h-100">
    <div class="card-body d-flex align-items-center">
        @if($icon)
            <i class="{{ $icon }} fa-2x me-3" aria-hidden="true"></i>
        @endif
        <div>
            <div class="h6 mb-1 text-uppercase">{{ $label }}</div>
            <div class="h4 mb-0">{{ $value }}</div>
        </div>
    </div>
</div>
