<script>
  // Vérification du rôle superadmin avant affichage
  if (localStorage.getItem('type_utilisateur') !== 'superadmin') {
    window.location.href = '/unauthorized';
  }
</script>

@include('includes.header')
@include('includes.sidebar_superadmin')

<main class="content">
  <header class="page-header">
    <h1>Organismes partenaires</h1>
    <div class="user-info">
      <span class="user-avatar" id="user-avatar"></span>
      <span id="user-name"></span>
    </div>
  </header>

  <script>
    // Affichage avatar et nom
    let nom = localStorage.nom_utilisateur || 'Super Admin';
    let prenom = localStorage.prenom_utilisateur || '';
    let fullName = (prenom + ' ' + nom).trim();
    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random&length=2&color=ffffff&rounded=true`;
    document.getElementById('user-avatar').innerHTML = `<img src="${avatarUrl}" alt="${fullName}" style="width:48px;height:48px;border-radius:50%;">`;
    document.getElementById('user-name').textContent = fullName;
  </script>

  <section>
    <h2>Liste des organismes</h2>

    <!-- Bouton Création (hors table pour affichage fiable) -->
    <div style="margin-bottom: 1rem;">
      <button id="btn-add-organisme-table" title="Créer un organisme" style="display:none; background:#007bff; color:#fff; border:none; border-radius:5px; padding:10px 15px; font-size:1rem; cursor:pointer;">
        + Créer un organisme
      </button>
    </div>

    <div class="table-wrapper">
      <table class="styled-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Domaine</th>
            <th>Responsable</th>
            <th>Signalements</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="organismes-table-body">
          <tr><td colspan="7">Chargement...</td></tr>
        </tbody>
      </table>
    </div>

    <script>
      function isAdmin() {
        return localStorage.getItem('type_utilisateur') === 'superadmin';
      }

      async function fetchAndRenderOrganismes() {
        const token = localStorage.getItem('auth_token');
        const tableBody = document.getElementById('organismes-table-body');
        tableBody.innerHTML = '<tr><td colspan="7">Chargement...</td></tr>';

        try {
          const response = await fetch('/api/v1/organismes', {
            headers: {
              'Authorization': 'Bearer ' + token,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            tableBody.innerHTML = '<tr><td colspan="7">Erreur lors du chargement</td></tr>';
            return;
          }

          const organismes = await response.json();

          if (!organismes.length) {
            tableBody.innerHTML = '<tr><td colspan="7">Aucun organisme trouvé</td></tr>';
            return;
          }

          tableBody.innerHTML = organismes.map((o, i) => `
            <tr>
              <td>${i + 1}</td>
              <td>${o.nom_organisme}</td>
              <td>${o.domaine_organisme || '-'}</td>
              <td>${o.email_organisme || '-'}</td>
              <td>${typeof o.signalements_count !== 'undefined' ? o.signalements_count : (o.nombre_signalements)}</td>
              <td><span class="status" style="color:${o.statut_organisme === 'désactivé' ? 'red' : 'green'};font-weight:bold;">${o.statut_organisme === 'désactivé' ? 'Désactivé' : 'Activé'}</span></td>
              <td>
                ${isAdmin() ? `
                  <button class='btn-sm edit' data-id='${o.id_organisme}'>Modifier</button>
                  ${o.statut_organisme === 'activé'
                    ? `<button class='btn-sm toggle-status' data-id='${o.id_organisme}' data-action='disable' style='background:#dc3545;color:#fff;margin-left:5px;'>Désactiver</button>`
                    : `<button class='btn-sm toggle-status' data-id='${o.id_organisme}' data-action='enable' style='background:#28a745;color:#fff;margin-left:5px;'>Activer</button>`
                  }
                ` : ''}
              </td>
            </tr>
          `).join('');

          if (isAdmin()) {
            document.querySelectorAll('.btn-sm.edit').forEach(btn => {
              btn.onclick = function() { showEditOrganismeModal(this.dataset.id); };
            });
            document.querySelectorAll('.btn-sm.toggle-status').forEach(btn => {
              btn.onclick = async function() {
                const id = this.dataset.id;
                const action = this.dataset.action;
                const token = localStorage.getItem('auth_token');
                let confirmMsg = action === 'disable'
                  ? "Voulez-vous vraiment désactiver cet organisme ?"
                  : "Voulez-vous vraiment activer cet organisme ?";
                
                showStatutConfirmModal({
                  id: id,
                  action: action,
                  confirmMsg: confirmMsg,
                  onConfirm: async function() {
                    try {
                      const res = await fetch(`/api/v1/organismes/${id}`, {
                        method: 'PATCH',
                        headers: {
                          'Authorization': 'Bearer ' + token,
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-Requested-With': 'XMLHttpRequest',
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                          statut_organisme: action === 'disable' ? 'désactivé' : 'activé'
                        })
                      });
                      
                      const data = await res.json();
                      
                      if (!res.ok) {
                        showPopover(data.message || "Erreur lors de la modification du statut", 'error');
                        return;
                      }
                      
                      showPopover(
                        action === 'disable' 
                          ? 'Organisme désactivé avec succès' 
                          : 'Organisme activé avec succès', 
                        'success'
                      );
                      
                      await fetchAndRenderOrganismes();
                    } catch (e) {
                      console.error("Erreur : ", e);
                      showPopover("Une erreur est survenue", 'error');
                    }
                  }
                });
              };
            });
            document.getElementById('btn-add-organisme-table').style.display = 'inline-block';
          }
        } catch (e) {
          tableBody.innerHTML = '<tr><td colspan="7">Erreur réseau</td></tr>';
        }
      }

      document.addEventListener('DOMContentLoaded', () => {
        fetchAndRenderOrganismes();
        if (isAdmin()) {
          document.getElementById('btn-add-organisme-table').style.display = 'inline-block';
        }
      });
    </script>

    <!-- Modal Ajout/Modification Organisme -->
    <div id="organismeModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
      <div style="background:#fff;padding:2rem;border-radius:8px;max-width:400px;margin:auto;text-align:center;">
        <h3 id="organismeModalTitle">Nouvel organisme</h3>
        <form id="organismeForm">
          <input type="hidden" name="id_organisme" id="org-id">
          <div style="margin-bottom:1rem;">
            <input type="text" name="nom_organisme" id="org-nom" placeholder="Nom de l'organisme" class="form-control" required style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'">
          </div>
          <div style="margin-bottom:1rem;">
            <input type="text" name="domaine_organisme" id="org-domaine" placeholder="Domaine (optionnel)" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'">
          </div>
          <div style="margin-bottom:1rem;">
            <input type="email" name="email_organisme" id="org-email" placeholder="Email du responsable" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'" required>
          </div>
          <div style="margin-bottom:1rem;">
            <input type="text" name="tel_organisme" id="org-tel" placeholder="Téléphone" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'" required>
          </div>
          <div style="margin-bottom:1rem;">
            <input type="text" name="adresse_organisme" id="org-adresse" placeholder="Adresse complète" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'" required>
          </div>
          <div style="margin-bottom:1rem;">
            <textarea name="description_organisme" id="org-desc" placeholder="Description (optionnelle)" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;min-height:60px;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;resize:vertical;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'"></textarea>
          </div>
          <button type="submit" class="btn btn-primary text-blue" id="org-save-btn">Enregistrer</button>
          <button type="button" onclick="hideOrganismeModal()" class="btn">Annuler</button>
        </form>
      </div>
    </div>
    <!-- Modal Suppression Organisme -->
    <div id="deleteOrgModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
      <div style="background:#fff;padding:2rem;border-radius:8px;max-width:350px;margin:auto;text-align:center;">
        <h3>Supprimer l'organisme ?</h3>
        <p>Cette action est irréversible.</p>
        <button id="confirmDeleteOrgBtn" class="btn btn-danger">Supprimer</button>
        <button onclick="hideDeleteOrganismeModal()" class="btn">Annuler</button>
      </div>
    </div>
    <script>
    // --- JS pour CRUD Organismes ---
    let editOrgId = null;
    function showEditOrganismeModal(id) {
      editOrgId = id;
      document.getElementById('organismeModalTitle').textContent = id ? 'Modifier l\'organisme' : 'Nouvel organisme';
      if (id) {
        // Charger infos organisme à modifier
        const token = localStorage.getItem('auth_token');
        fetch(`/api/v1/organismes/${id}`, {
          headers: { 
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
          }
        })
          .then(r => r.json())
          .then(o => {
            document.getElementById('org-id').value = o.id_organisme;
            document.getElementById('org-nom').value = o.nom_organisme || '';
            document.getElementById('org-domaine').value = o.domaine_organisme || '';
            document.getElementById('org-email').value = o.email_organisme || '';
            document.getElementById('org-tel').value = o.tel_organisme || '';
            document.getElementById('org-desc').value = o.description_organisme || '';
            document.getElementById('organismeModal').style.display = 'flex';
          });
      } else {
        document.getElementById('org-id').value = '';
        document.getElementById('org-nom').value = '';
        document.getElementById('org-domaine').value = '';
        document.getElementById('org-email').value = '';
        document.getElementById('org-tel').value = '';
        document.getElementById('org-desc').value = '';
        document.getElementById('organismeModal').style.display = 'flex';
      }
    }
    function hideOrganismeModal() {
      editOrgId = null;
      document.getElementById('organismeModal').style.display = 'none';
    }
    document.getElementById('btn-add-organisme-table').onclick = function() {
      showEditOrganismeModal(null);
    };
    document.getElementById('organismeForm').onsubmit = async function(e) {
      e.preventDefault();
      const token = localStorage.getItem('auth_token');
      if (!token) {
        console.error('Session expirée. Veuillez vous reconnecter.');
        window.location.href = '/login';
        return;
      }

      const id = document.getElementById('org-id').value;
      const data = {
        nom_organisme: document.getElementById('org-nom').value,
        adresse_organisme: document.getElementById('org-adresse').value,
        tel_organisme: document.getElementById('org-tel').value,
        email_organisme: document.getElementById('org-email').value,
        description_organisme: document.getElementById('org-desc').value,
        domaine_organisme: document.getElementById('org-domaine').value
      };

      // Validation des données
      if (!data.nom_organisme || !data.adresse_organisme || !data.tel_organisme || !data.email_organisme) {
        ;
        return;
      }

      try {
        const url = id ? `/api/v1/organismes/${id}` : '/api/v1/organismes';
        const method = id ? 'PATCH' : 'POST';
        
        console.log('Envoi de la requête:', { url, method, data });
        
        const response = await fetch(url, {
          method: method,
          headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(data)
        });

        const responseData = await response.json();
        console.log('Réponse du serveur:', responseData);

        if (!response.ok) {
          // Supprimer les anciens messages d'erreur
          document.querySelectorAll('.input-error').forEach(e => e.remove());
          if (responseData.errors) {
            // Afficher les nouveaux messages d'erreur sous les champs concernés
            Object.entries(responseData.errors).forEach(([field, messages]) => {
              // Mapping champ backend -> id champ input
              let inputId = '';
              switch(field) {
                case 'nom_organisme': inputId = 'org-nom'; break;
                case 'email_organisme': inputId = 'org-email'; break;
                case 'adresse_organisme': inputId = 'org-adresse'; break;
                case 'tel_organisme': inputId = 'org-tel'; break;
                case 'description_organisme': inputId = 'org-desc'; break;
                case 'domaine_organisme': inputId = 'org-domaine'; break;
                default: inputId = null;
              }
              if (inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                  const errorDiv = document.createElement('div');
                  errorDiv.className = 'input-error';
                  errorDiv.style.color = 'red';
                  errorDiv.style.fontSize = '0.9em';
                  errorDiv.style.marginTop = '2px';
                  errorDiv.textContent = messages.join(', ');
                  input.parentNode.appendChild(errorDiv);
                }
              }
            });
          } else {
            showPopover(responseData.message || 'Erreur lors de l\'enregistrement de l\'organisme', 'error');
          }
          return;
        }

        hideOrganismeModal();
        await fetchAndRenderOrganismes();
      } catch (e) {
        showPopover('Erreur lors de la requête: ' + e.message, 'error');
      }
    };
    // Suppression
    let deleteOrgId = null;
    function showDeleteOrganismeModal(id) {
      deleteOrgId = id;
      document.getElementById('deleteOrgModal').style.display = 'flex';
    }
    function hideDeleteOrganismeModal() {
      deleteOrgId = null;
      document.getElementById('deleteOrgModal').style.display = 'none';
    }
    document.getElementById('confirmDeleteOrgBtn').onclick = async function() {
      if (!deleteOrgId) return;
      const token = localStorage.getItem('auth_token');
      try {
        const res = await fetch(`/api/v1/organismes/${deleteOrgId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        hideDeleteOrganismeModal();
        fetchAndRenderOrganismes();
      } catch (e) {
        hideDeleteOrganismeModal();
      }
    };
    </script>

  </section>
  <style>
  /* Styles existants */
  .modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center; }
  .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 0 5px; }
  .btn-primary { background: #007bff; color: white; }
  .btn-danger { background: #dc3545; color: white; }
  
  /* Styles pour les popovers */
  .popover {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 5px;
    color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    z-index: 1100;
    animation: slideIn 0.3s ease-out;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .popover.success { background-color: #28a745; }
  .popover.error { background-color: #dc3545; }
  .popover.warning { background-color: #ffc107; color: #212529; }
  
  .popover-close {
    background: none;
    border: none;
    color: inherit;
    font-size: 1.2em;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
  }
  
  @keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
  
  @keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
  }
</style>
  <script>
    // Fonction pour afficher une boîte de confirmation
    function showStatutConfirmModal({ id, action, confirmMsg, onConfirm }) {
      // Créer la modale de confirmation si elle n'existe pas
      let modal = document.getElementById('confirmStatusModal');
      
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'confirmStatusModal';
        modal.className = 'modal';
        modal.style.display = 'none';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modal.style.zIndex = '2000';
        modal.style.display = 'flex';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        
        const modalContent = document.createElement('div');
        modalContent.style.backgroundColor = '#fff';
        modalContent.style.padding = '2rem';
        modalContent.style.borderRadius = '8px';
        modalContent.style.maxWidth = '400px';
        modalContent.style.width = '90%';
        modalContent.style.textAlign = 'center';
        
        modalContent.innerHTML = `
          <h3>Confirmer l'action</h3>
          <p id="confirmStatusMessage">${confirmMsg}</p>
          <div style="margin-top: 1.5rem;">
            <button id="confirmStatusYes" class="btn" style="background: #dc3545; color: white; margin-right: 10px;">Oui</button>
            <button id="confirmStatusNo" class="btn" style="background: #6c757d; color: white;">Annuler</button>
          </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Gestion des clics sur les boutons
        document.getElementById('confirmStatusYes').addEventListener('click', function() {
          modal.style.display = 'none';
          if (typeof onConfirm === 'function') {
            onConfirm();
          }
        });
        
        document.getElementById('confirmStatusNo').addEventListener('click', function() {
          modal.style.display = 'none';
        });
      } else {
        // Mettre à jour le message et afficher la modale existante
        document.getElementById('confirmStatusMessage').textContent = confirmMsg;
        modal.style.display = 'flex';
        
        // Mettre à jour le gestionnaire d'événements pour le bouton Oui
        const oldBtn = document.getElementById('confirmStatusYes');
        const newBtn = oldBtn.cloneNode(true);
        oldBtn.parentNode.replaceChild(newBtn, oldBtn);
        
        newBtn.addEventListener('click', function() {
          modal.style.display = 'none';
          if (typeof onConfirm === 'function') {
            onConfirm();
          }
        });
      }
      
      // Afficher la modale
      modal.style.display = 'flex';
    }

    // Fonction pour afficher les popovers
    function showPopover(message, type = 'success') {
      // Supprimer les popovers existants
      document.querySelectorAll('.popover').forEach(el => el.remove());
      
      // Créer le popover
      const popover = document.createElement('div');
      popover.className = `popover ${type}`;
      
      // Icône en fonction du type
      let icon = '✓';
      if (type === 'error') icon = '✗';
      else if (type === 'warning') icon = '!';
      
      popover.innerHTML = `
        <span>${icon}</span>
        <span>${message}</span>
        <button class="popover-close">&times;</button>
      `;
      
      // Ajouter le popover au document
      document.body.appendChild(popover);
      
      // Fermer le popover au clic sur le bouton de fermeture
      const closeBtn = popover.querySelector('.popover-close');
      closeBtn.addEventListener('click', () => {
        popover.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => popover.remove(), 300);
      });
      
      // Fermer automatiquement après 5 secondes
      setTimeout(() => {
        if (popover.parentNode) {
          popover.style.animation = 'fadeOut 0.3s ease-out';
          setTimeout(() => popover.remove(), 300);
        }
      }, 5000);
    }
    
    // Script de débogage temporaire
    window.addEventListener('DOMContentLoaded', function() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      console.log('CSRF Token element:', csrfToken);
      if (csrfToken) {
        console.log('CSRF Token value:', csrfToken.getAttribute('content'));
      } else {
        console.error('Aucune balise meta CSRF trouvée dans le document');
      }
    });
</script>

  </main>

@include('includes.footer')
