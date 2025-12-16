@extends('layouts.app')

@section('title', 'Tableau de bord Administrateur')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tableau de bord Administrateur</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Imprimer
            </button>
            <button class="btn btn-success" onclick="exportData()">
                <i class="bi bi-download me-2"></i> Exporter
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Utilisateurs Total</h6>
                            <h3 class="mb-0">{{ number_format($stats['users_total']) }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i>
                                +{{ $stats['users_new_month'] }} ce mois
                            </small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people-fill text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Consultations Aujourd'hui</h6>
                            <h3 class="mb-0">{{ number_format($stats['consultations_today']) }}</h3>
                            <small class="text-muted">
                                Total mois: {{ number_format($stats['consultations_month']) }}
                            </small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clipboard-pulse text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Revenus du Jour</h6>
                            <h3 class="mb-0">{{ number_format($stats['revenue_today']) }} FCFA</h3>
                            <small class="text-info">
                                Mois: {{ number_format($stats['revenue_month']) }} FCFA
                            </small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cash-stack text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Structures Vérifiées</h6>
                            <h3 class="mb-0">{{ $stats['structures_verified'] }}/{{ $stats['structures_total'] }}</h3>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar bg-info" style="width: {{ ($stats['structures_verified']/$stats['structures_total'])*100 }}%"></div>
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building-check text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($systemAlerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Alertes Système
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($systemAlerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div class="flex-grow-1">{{ $alert['message'] }}</div>
                        @if(isset($alert['link']))
                        <a href="{{ $alert['link'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }}">
                            Voir <i class="bi bi-arrow-right"></i>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Évolution des Revenus (30 derniers jours)</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" data-period="day">Jour</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="week">Semaine</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="month">Mois</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="col-lg-4">
            <div class="card border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Répartition par Rôle</h5>
                </div>
                <div class="card-body">
                    <canvas id="roleChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($charts['roleDistribution'] as $role)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ ucfirst($role->role) }}</span>
                            <strong>{{ $role->count }}</strong>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Statistics -->
    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="card border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Activités Récentes</h5>
                    <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($activity->prenom ?? '', 0, 1) }}{{ substr($activity->nom ?? '', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $activity->prenom }} {{ $activity->nom }}</div>
                                                <small class="text-muted">{{ $activity->user_role }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->action == 'create' ? 'success' : ($activity->action == 'delete' ? 'danger' : 'info') }}">
                                            {{ $activity->action }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->module }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques Rapides</h5>
                </div>
                <div class="card-body">
                    <!-- Consultation Types -->
                    <h6 class="text-muted mb-3">Types de Consultations (Ce mois)</h6>
                    @foreach($charts['consultationTypes'] as $type)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>{{ ucfirst($type->type) }}</small>
                            <small class="fw-bold">{{ $type->count }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($type->count / $charts['consultationTypes']->sum('count')) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach

                    <hr class="my-4">

                    <!-- User Growth -->
                    <h6 class="text-muted mb-3">Croissance Utilisateurs</h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1 text-success">+{{ $stats['users_new_month'] }}</h4>
                            <small class="text-muted">Ce mois</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $stats['users_active'] }}</h4>
                            <small class="text-muted">Actifs</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Quick Actions -->
                    <h6 class="text-muted mb-3">Actions Rapides</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person-plus me-2"></i> Nouvel Utilisateur
                        </a>
                        <a href="{{ route('structures.create') }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-building-add me-2"></i> Nouvelle Structure
                        </a>
                        <a href="{{ route('rapports.create') }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> Générer Rapport
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['dailyRevenue']->pluck('date')) !!},
            datasets: [{
                label: 'Revenus (FCFA)',
                data: {!! json_encode($charts['dailyRevenue']->pluck('total')) !!},
                borderColor: 'rgb(46, 125, 50)',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenus: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                }
            }
        }
    });

    // Role Distribution Chart
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    const roleChart = new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($charts['roleDistribution']->pluck('role')) !!},
            datasets: [{
                data: {!! json_encode($charts['roleDistribution']->pluck('count')) !!},
                backgroundColor: [
                    'rgba(46, 125, 50, 0.8)',
                    'rgba(21, 101, 192, 0.8)',
                    'rgba(251, 140, 0, 0.8)',
                    'rgba(229, 57, 53, 0.8)',
                    'rgba(3, 155, 229, 0.8)',
                    'rgba(67, 160, 71, 0.8)',
                    'rgba(156, 39, 176, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Period switcher for revenue chart
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Here you would typically fetch new data based on the period
            // and update the chart
        });
    });

    // Export function
    function exportData() {
        Swal.fire({
            title: 'Exporter les données',
            text: 'Choisissez le format d\'export',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Excel',
            cancelButtonText: 'PDF',
            showCloseButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("rapports.export", ["format" => "excel"]) }}';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '{{ route("rapports.export", ["format" => "pdf"]) }}';
            }
        });
    }
</script>
@endpush
@endsection
