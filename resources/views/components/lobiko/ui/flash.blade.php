@if(session('success') || session('error') || session('warning') || session('info'))
    @php
        $types = ['success', 'error', 'warning', 'info'];
    @endphp
    @foreach($types as $type)
        @if(session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
@endif
