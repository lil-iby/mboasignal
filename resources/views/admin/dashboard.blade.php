@extends('layouts.admin')

@section('title', 'Tableau de bord Administrateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête du tableau de bord -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord Administrateur</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Générer un rapport
        </a>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row">
        <!-- Signalements en attente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Signalements en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-reports">Chargement...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs actifs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Utilisateurs actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-users">Chargement...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signalements traités -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Signalements traités</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolved-reports">Chargement...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signalements ce mois-ci -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Signalements (30j)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthly-reports">Chargement...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="row">
        <!-- Graphique des signalements -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu des signalements</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières activités -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières activités</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="recent-activities">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des derniers signalements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Derniers signalements</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="recentReportsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Localisation</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recent-reports-body">
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Chargement...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Scripts spécifiques au tableau de bord -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour charger les données du tableau de bord
    async function loadDashboardData() {
        try {
            const token = localStorage.getItem('auth_token');
            
            // Charger les statistiques
            const statsResponse = await fetch('/api/v1/dashboard/stats', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const stats = await statsResponse.json();
            
            if (stats.status === 'success') {
                // Mettre à jour les cartes de statistiques
                document.getElementById('pending-reports').textContent = stats.data.pending_reports || 0;
                document.getElementById('active-users').textContent = stats.data.active_users || 0;
                document.getElementById('resolved-reports').textContent = stats.data.resolved_reports || 0;
                document.getElementById('monthly-reports').textContent = stats.data.monthly_reports || 0;
            }
            
            // Charger les activités récentes
            const activitiesResponse = await fetch('/api/v1/dashboard/recent-activities', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const activities = await activitiesResponse.json();
            
            if (activities.status === 'success') {
                const activitiesContainer = document.getElementById('recent-activities');
                activitiesContainer.innerHTML = '';
                
                activities.data.forEach(activity => {
                    const activityElement = document.createElement('a');
                    activityElement.href = '#';
                    activityElement.className = 'list-group-item list-group-item-action flex-column align-items-start';
                    
                    const timeAgo = new Date(activity.created_at).toLocaleTimeString('fr-FR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    activityElement.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${activity.title}</h6>
                            <small>${timeAgo}</small>
                        </div>
                        <p class="mb-1">${activity.description}</p>
                        <small>${activity.user_name}</small>
                    `;
                    
                    activitiesContainer.appendChild(activityElement);
                });
            }
            
            // Charger les derniers signalements
            const reportsResponse = await fetch('/api/v1/dashboard/recent-reports', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const reports = await reportsResponse.json();
            
            if (reports.status === 'success') {
                const reportsBody = document.getElementById('recent-reports-body');
                reportsBody.innerHTML = '';
                
                reports.data.forEach(report => {
                    const row = document.createElement('tr');
                    
                    const formattedDate = new Date(report.created_at).toLocaleDateString('fr-FR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    row.innerHTML = `
                        <td>#${report.id}</td>
                        <td>${report.type}</td>
                        <td>${report.description.substring(0, 50)}${report.description.length > 50 ? '...' : ''}</td>
                        <td>${report.location || 'Non spécifiée'}</td>
                        <td><span class="badge badge-${getStatusBadgeClass(report.status)}">${report.status}</span></td>
                        <td>${formattedDate}</td>
                        <td>
                            <a href="/admin/reports/${report.id}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    `;
                    
                    reportsBody.appendChild(row);
                });
            }
            
            // Initialiser le graphique des signalements
            initializeReportsChart(stats?.data?.reports_by_month || []);
            
        } catch (error) {
            console.error('Erreur lors du chargement du tableau de bord:', error);
            // Afficher un message d'erreur à l'utilisateur
        }
    }
    
    // Fonction pour initialiser le graphique des signalements
    function initializeReportsChart(data) {
        const ctx = document.getElementById('reportsChart');
        
        // Préparer les données pour le graphique
        const labels = data.map(item => item.month);
        const counts = data.map(item => item.count);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Signalements',
                    data: counts,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        grid: {
                            color: 'rgb(234, 236, 244)',
                            zeroLineColor: 'rgb(234, 236, 244)',
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyColor: '#858796',
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10
                    }
                }
            }
        });
    }
    
    // Fonction utilitaire pour obtenir la classe CSS du badge en fonction du statut
    function getStatusBadgeClass(status) {
        const statusClasses = {
            'en_attente': 'warning',
            'en_cours': 'info',
            'résolu': 'success',
            'fermé': 'secondary',
            'rejeté': 'danger'
        };
        
        return statusClasses[status.toLowerCase()] || 'secondary';
    }
    
    // Charger les données du tableau de bord au chargement de la page
    loadDashboardData();
});
</script>
@endpush
