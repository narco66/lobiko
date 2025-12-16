@extends('layouts.app')

@section('title', 'Demandes publiques')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <p class="text-uppercase text-primary fw-semibold small mb-1">Demandes</p>
        <h1 class="h4 fw-bold mb-2">Demandes publiques (Pharmacie, Assurance, Urgence)</h1>
        <p class="text-muted mb-0">Liste filtrable, export CSV et SMS/Mail envoyés à l'admin.</p>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">Tous</option>
                <option value="pharmacy" @selected(request('type')==='pharmacy')>Pharmacie</option>
                <option value="insurance" @selected(request('type')==='insurance')>Assurance</option>
                <option value="emergency" @selected(request('type')==='emergency')>Urgence</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="status" class="form-select">
                <option value="">Tous</option>
                <option value="pending" @selected(request('status')==='pending')>En attente</option>
                <option value="in_progress" @selected(request('status')==='in_progress')>En cours</option>
                <option value="done" @selected(request('status')==='done')>Traité</option>
                <option value="rejected" @selected(request('status')==='rejected')>Rejeté</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Du</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Au</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Recherche</label>
            <input type="text" name="q" class="form-control" placeholder="Nom/téléphone/email" value="{{ request('q') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary w-100" type="submit">Filtrer</button>
            <a href="{{ route('admin.requests.export', ['type' => request('type','pharmacy')]) }}" class="btn btn-outline-secondary" title="Export CSV">
                Export
            </a>
        </div>
    </form>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Rendez-vous</h5>
            @php($appointments = \App\Models\AppointmentRequest::latest()->paginate(20, ['*'], 'appointments_page'))
            @forelse($appointments as $item)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="fw-semibold">{{ $item->full_name }} — {{ $item->phone }}</div>
                    <div class="text-muted small">{{ $item->created_at?->format('d/m/Y H:i') }}</div>
                    <div class="small">Spécialité : {{ $item->speciality }}</div>
                    <div class="small">Mode : {{ ucfirst($item->mode) }}</div>
                    <div class="small">Date souhaitée : {{ $item->preferred_date?->format('d/m/Y') }}</div>
                </div>
            @empty
                <p class="text-muted mb-0">Aucune demande de rendez-vous.</p>
            @endforelse
            {{ $appointments->appends(request()->query())->links() }}
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Pharmacie</h5>
                    @forelse($pharmacy as $item)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-semibold">{{ $item->full_name }} — {{ $item->phone }}</div>
                            <div class="text-muted small">{{ $item->created_at?->format('d/m/Y H:i') }}</div>
                            <div class="small">Mode : {{ ucfirst($item->delivery_mode) }}</div>
                            @if($item->prescription_code)
                                <div class="small">Ordonnance : {{ $item->prescription_code }}</div>
                            @endif
                            @if($item->address)
                                <div class="small">Adresse : {{ $item->address }}</div>
                            @endif
                            <form method="POST" action="{{ route('admin.requests.status') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="type" value="pharmacy">
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <select name="status" class="form-select form-select-sm w-auto d-inline-block">
                                    @foreach(['pending'=>'En attente','in_progress'=>'En cours','done'=>'Traité','rejected'=>'Rejeté'] as $val=>$label)
                                        <option value="{{ $val }}" @selected($item->status===$val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary ms-1" type="submit">OK</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune demande.</p>
                    @endforelse
                    {{ $pharmacy->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Assurance</h5>
                    @forelse($insurance as $item)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-semibold">{{ $item->full_name }} — {{ $item->phone }}</div>
                            <div class="text-muted small">{{ $item->created_at?->format('d/m/Y H:i') }}</div>
                            <div class="small">Police : {{ $item->policy_number }}</div>
                            <div class="small">Type : {{ ucfirst($item->request_type) }}</div>
                            @if($item->insurer)
                                <div class="small">Assureur : {{ $item->insurer }}</div>
                            @endif
                            <form method="POST" action="{{ route('admin.requests.status') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="type" value="insurance">
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <select name="status" class="form-select form-select-sm w-auto d-inline-block">
                                    @foreach(['pending'=>'En attente','in_progress'=>'En cours','done'=>'Traité','rejected'=>'Rejeté'] as $val=>$label)
                                        <option value="{{ $val }}" @selected($item->status===$val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary ms-1" type="submit">OK</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune demande.</p>
                    @endforelse
                    {{ $insurance->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Urgence</h5>
                    @forelse($emergency as $item)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fw-semibold">{{ $item->full_name }} — {{ $item->phone }}</div>
                            <div class="text-muted small">{{ $item->created_at?->format('d/m/Y H:i') }}</div>
                            <div class="small">Type : {{ ucfirst($item->emergency_type) }}</div>
                            @if($item->city || $item->location)
                                <div class="small">Lieu : {{ $item->city }} {{ $item->location }}</div>
                            @endif
                            <form method="POST" action="{{ route('admin.requests.status') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="type" value="emergency">
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <select name="status" class="form-select form-select-sm w-auto d-inline-block">
                                    @foreach(['pending'=>'En attente','in_progress'=>'En cours','done'=>'Traité','rejected'=>'Rejeté'] as $val=>$label)
                                        <option value="{{ $val }}" @selected($item->status===$val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary ms-1" type="submit">OK</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune demande.</p>
                    @endforelse
                    {{ $emergency->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
