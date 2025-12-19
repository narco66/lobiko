@props([
    'title',
    'breadcrumbs' => [],
    'action' => null, // ['label' => '', 'href' => '', 'icon' => '', 'can' => null]
])

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">{{ $title }}</h1>
        @if($breadcrumbs && count($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @foreach($breadcrumbs as $crumb)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" @if($loop->last) aria-current="page" @endif>
                            @if(!empty($crumb['href']) && !$loop->last)
                                <a href="{{ $crumb['href'] }}">{{ $crumb['label'] }}</a>
                            @else
                                {{ $crumb['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
    </div>
    @if($action && (empty($action['can']) || auth()->user()?->can($action['can'])))
        <x-ui.button :href="$action['href'] ?? '#'" variant="primary" :icon="$action['icon'] ?? null">
            {{ $action['label'] ?? '' }}
        </x-ui.button>
    @endif
</div>
