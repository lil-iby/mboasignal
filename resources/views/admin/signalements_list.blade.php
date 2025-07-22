@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Signalements</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSignalementModal">
            <i class="fas fa-plus"></i> Nouveau Signalement
        </button>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Tous les statuts</option>
                        <option value="nouveau">Nouveau</option>
                        <option value="en_cours">En cours</option>
                        <option value="traite">Traité</option>
                        <option value="ferme">Fermé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <select class="form-select" id="filterCategorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id_categorie }}">{{ $categorie->nom_categorie }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="filterDateDebut">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="filterDateFin">
                        <button class="btn btn-outline-secondary" type="button" id="btnFiltrer">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des signalements -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="signalementsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Localisation</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les données seront chargées via JavaScript -->
                        <tr>
                            <td colspan="7" class="text-center">Chargement des données...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav class="mt-3">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- La pagination sera générée par JavaScript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Détails du Signalement -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Signalement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="modalDetailsBody">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="btnModifier">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout/Modification -->
<div class="modal fade" id="signalementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="signalementForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nouveau Signalement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="signalement_id" name="id">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre_signalement" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description_signalement" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="localisation" class="form-label">Localisation *</label>
                        <input type="text" class="form-control" id="localisation" name="localisation_signalement" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categorie" class="form-label">Catégorie *</label>
                            <select class="form-select" id="categorie" name="categorie_id" required>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id_categorie }}">{{ $categorie->nom_categorie }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut *</label>
                            <select class="form-select" id="statut" name="etat_signalement" required>
                                <option value="nouveau">Nouveau</option>
                                <option value="en_cours">En cours</option>
                                <option value="traite">Traité</option>
                                <option value="ferme">Fermé</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fichiers" class="form-label">Fichiers (images, documents)</label>
                        <input class="form-control" type="file" id="fichiers" name="fichiers[]" multiple>
                        <div id="fichiersList" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const token = localStorage.getItem('auth_token');
    let currentPage = 1;
    const perPage = 10;

    // Initialisation de DataTables
    const table = $('#signalementsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/v1/signalements',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            data: function(d) {
                d.status = $('#filterStatus').val();
                d.categorie_id = $('#filterCategorie').val();
                d.date_debut = $('#filterDateDebut').val();
                d.date_fin = $('#filterDateFin').val();
            }
        },
        columns: [
            { data: 'id_signalement' },
            { data: 'nom_signalement' },
            { 
                data: 'description_signalement',
                render: function(data, type, row) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            { data: 'localisation_signalement' },
            { 
                data: 'date_signalement',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'etat_signalement',
                render: function(data) {
                    const statusMap = {
                        'nouveau': 'Nouveau',
                        'en_cours': 'En cours',
                        'traite': 'Traité',
                        'ferme': 'Fermé'
                    };
                    const statusClass = {
                        'nouveau': 'bg-primary',
                        'en_cours': 'bg-warning',
                        'traite': 'bg-success',
                        'ferme': 'bg-secondary'
                    };
                    return `<span class="badge ${statusClass[data] || 'bg-secondary'}">${statusMap[data] || data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info btn-view" data-id="${row.id_signalement}" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id_signalement}" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_signalement}" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']]
    });

    // Gestion des filtres
    $('#btnFiltrer').click(function() {
        table.ajax.reload();
    });

    // Réinitialisation des filtres
    $('#btnResetFilters').click(function() {
        $('#filterStatus, #filterCategorie, #filterDateDebut, #filterDateFin').val('');
        table.ajax.reload();
    });

    // Afficher les détails d'un signalement
    $(document).on('click', '.btn-view', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/v1/signalements/${id}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                const signalement = response.data;
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID :</strong> ${signalement.id_signalement}</p>
                            <p><strong>Titre :</strong> ${signalement.nom_signalement}</p>
                            <p><strong>Description :</strong> ${signalement.description_signalement}</p>
                            <p><strong>Localisation :</strong> ${signalement.localisation_signalement}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date :</strong> ${new Date(signalement.date_signalement).toLocaleString()}</p>
                            <p><strong>Statut :</strong> <span class="badge bg-${getStatusClass(signalement.etat_signalement)}">${formatStatus(signalement.etat_signalement)}</span></p>
                            <p><strong>Catégorie :</strong> ${signalement.categorie?.nom_categorie || 'Non spécifiée'}</p>
                        </div>
                    </div>
                `;

                // Afficher les médias s'il y en a
                if (signalement.medias && signalement.medias.length > 0) {
                    html += '<div class="mt-3"><h5>Médias associés :</h5><div class="row">';
                    signalement.medias.forEach(media => {
                        if (media.type_media === 'image') {
                            html += `
                                <div class="col-md-3 mb-2">
                                    <img src="${media.url_media}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="col-md-3 mb-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file fa-3x mb-2"></i>
                                            <p class="mb-0">${media.nom_media}</p>
                                            <a href="${media.url_media}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    html += '</div></div>';
                }

                $('#modalDetailsBody').html(html);
                $('#detailsModal').modal('show');
            },
            error: function(xhr) {
                showError('Erreur lors du chargement des détails du signalement');
            }
        });
    });

    // Gestion de l'ajout d'un nouveau signalement
    $('#addSignalementBtn').click(function() {
        $('#signalementForm')[0].reset();
        $('#signalement_id').val('');
        $('#modalTitle').text('Nouveau Signalement');
        $('#signalementModal').modal('show');
    });

    // Gestion de la soumission du formulaire
    $('#signalementForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#signalement_id').val();
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/v1/signalements/${id}` : '/api/v1/signalements';

        // Ajouter les fichiers au FormData
        const files = $('#fichiers')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('fichiers[]', files[i]);
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#signalementModal').modal('hide');
                showSuccess(id ? 'Signalement mis à jour avec succès' : 'Signalement créé avec succès');
                table.ajax.reload();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMessage = 'Une erreur est survenue';
                
                if (xhr.status === 422) {
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showError(errorMessage);
            }
        });
    });

    // Gestion de la modification d'un signalement
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/v1/signalements/${id}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                const signalement = response.data;
                $('#signalement_id').val(signalement.id_signalement);
                $('#titre').val(signalement.nom_signalement);
                $('#description').val(signalement.description_signalement);
                $('#localisation').val(signalement.localisation_signalement);
                $('#categorie').val(signalement.id_categorie);
                $('#statut').val(signalement.etat_signalement);
                
                // Afficher les fichiers existants
                let filesHtml = '';
                if (signalement.medias && signalement.medias.length > 0) {
                    filesHtml += '<div class="mt-2"><strong>Fichiers existants :</strong><div class="row">';
                    signalement.medias.forEach(media => {
                        filesHtml += `
                            <div class="col-md-3 mb-2">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-truncate">${media.nom_media}</small>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 btn-delete-media" data-id="${media.id_media}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    filesHtml += '</div></div>';
                }
                $('#fichiersList').html(filesHtml);
                
                $('#modalTitle').text('Modifier le Signalement');
                $('#signalementModal').modal('show');
            },
            error: function() {
                showError('Erreur lors du chargement du signalement');
            }
        });
    });

    // Gestion de la suppression d'un signalement
    $(document).on('click', '.btn-delete', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?')) {
            const id = $(this).data('id');
            $.ajax({
                url: `/api/v1/signalements/${id}`,
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    showSuccess('Signalement supprimé avec succès');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    showError('Erreur lors de la suppression du signalement');
                }
            });
        }
    });

    // Gestion de la suppression d'un média
    $(document).on('click', '.btn-delete-media', function(e) {
        e.preventDefault();
        const mediaId = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
            $.ajax({
                url: `/api/v1/medias/${mediaId}`,
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $(`#media-${mediaId}`).remove();
                    showSuccess('Média supprimé avec succès');
                },
                error: function() {
                    showError('Erreur lors de la suppression du média');
                }
            });
        }
    });

    // Fonctions utilitaires
    function formatStatus(status) {
        const statusMap = {
            'nouveau': 'Nouveau',
            'en_cours': 'En cours',
            'traite': 'Traité',
            'ferme': 'Fermé'
        };
        return statusMap[status] || status;
    }

    function getStatusClass(status) {
        const statusClass = {
            'nouveau': 'primary',
            'en_cours': 'warning',
            'traite': 'success',
            'ferme': 'secondary'
        };
        return statusClass[status] || 'secondary';
    }

    function showSuccess(message) {
        const toast = `
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto">Succès</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fermer"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        $('body').append(toast);
        setTimeout(() => $('.toast-container').remove(), 3000);
    }

    function showError(message) {
        const toast = `
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger text-white">
                        <strong class="me-auto">Erreur</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fermer"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        $('body').append(toast);
        setTimeout(() => $('.toast-container').remove(), 5000);
    }
});
</script>
@endpush
