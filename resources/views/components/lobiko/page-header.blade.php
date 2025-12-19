@props([
    'title' => '',
    'breadcrumbs' => [],
    // Single action remains supported for backward compatibility
    'action' => null, // ['label' => '', 'href' => '', 'icon' => '']
    // Preferred: list of actions [['label' => '', 'url' => '', 'icon' => '', 'type' => 'primary']]
    'actions' => [],
])

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">{{ $title }}</h1>
        @if(!empty($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @foreach($breadcrumbs as $crumb)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" {{ $loop->last ? 'aria-current=page' : '' }}>
                            @if(!empty($crumb['href']) && !$loop->last)
                                <a href="{{ $crumb['href'] }}">{{ $crumb['label'] ?? '' }}</a>
                            @else
                                {{ $crumb['label'] ?? '' }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
    </div>
    <div class="d-flex gap-2">
        @foreach($actions as $item)
            @php
                $type = $item['type'] ?? 'primary';
                $icon = $item['icon'] ?? null;
            @endphp
            @if($type === 'secondary')
                <x-lobiko.buttons.secondary :href="$item['url'] ?? '#'" :icon="$icon">
                    {{ $item['label'] ?? 'Action' }}
                </x-lobiko.buttons.secondary>
            @else
                <x-lobiko.buttons.primary :href="$item['url'] ?? '#'" :icon="$icon">
                    {{ $item['label'] ?? 'Action' }}
                </x-lobiko.buttons.primary>
            @endif
        @endforeach

        @if($action && empty($actions))
            <x-lobiko.buttons.primary :href="$action['href'] ?? '#'" :icon="$action['icon'] ?? null">
                {{ $action['label'] ?? 'Action' }}
            </x-lobiko.buttons.primary>
        @endif
    </div>
</div>
