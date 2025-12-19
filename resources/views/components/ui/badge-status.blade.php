@props(['status' => ''])

@php
    $map = [
        'en_attente' => 'warning',
        'en_cours' => 'info',
        'terminee' => 'success',
        'annulee' => 'secondary',
        'brouillon' => 'secondary',
        'validee' => 'success',
        'signee' => 'success',
        'expiree' => 'danger',
        'initie' => 'info',
        'confirme' => 'success',
        'echoue' => 'danger',
        'rembourse' => 'primary',
    ];
    $color = $map[$status] ?? 'secondary';
@endphp

<span {{ $attributes->merge(['class' => "badge bg-{$color} text-uppercase"]) }}>
    {{ $slot ?: str_replace('_', ' ', $status) }}
</span>
