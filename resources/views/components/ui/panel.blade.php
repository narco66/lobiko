@props(['title' => '', 'actions' => null])

<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ $title }}</span>
        @if($actions)
            <div class="d-flex gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
