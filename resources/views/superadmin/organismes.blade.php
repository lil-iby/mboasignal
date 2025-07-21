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
              <td>${o.nb_signalements || '-'}</td>
              <td><span class="status active">Actif</span></td>
              <td>
                ${isAdmin() ? `
                <button class='btn-sm edit' data-id='${o.id_organisme}'>Modifier</button>
                <button class='btn-sm delete' data-id='${o.id_organisme}'>Supprimer</button>` : ''}
              </td>
            </tr>
          `).join('');

          if (isAdmin()) {
            document.querySelectorAll('.btn-sm.edit').forEach(btn => {
              btn.onclick = function() { showEditOrganismeModal(this.dataset.id); };
            });
            document.querySelectorAll('.btn-sm.delete').forEach(btn => {
              btn.onclick = function() { showDeleteOrganismeModal(this.dataset.id); };
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
            <input type="text" name="domaine_organisme" id="org-domaine" placeholder="Domaine" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'">
          </div>
          <div style="margin-bottom:1rem;">
            <input type="email" name="email_organisme" id="org-email" placeholder="Email du responsable" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'">
          </div>
          <div style="margin-bottom:1rem;">
            <input type="text" name="tel_organisme" id="org-tel" placeholder="Téléphone" class="form-control" style="border:1px solid #ccc;border-radius:7px;padding:10px 14px;margin-bottom:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.2s;outline:none;font-size:1rem;" onfocus="this.style.borderColor='#007bff'" onblur="this.style.borderColor='#ccc'">
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
      const id = document.getElementById('org-id').value;
      const data = {
        nom_organisme: document.getElementById('org-nom').value,
        domaine_organisme: document.getElementById('org-domaine').value,
        email_organisme: document.getElementById('org-email').value,
        tel_organisme: document.getElementById('org-tel').value,
        description_organisme: document.getElementById('org-desc').value
      };
      try {
        let response;
        if (id) {
          response = await fetch(`/api/v1/organismes/${id}`, {
            method: 'PATCH',
            headers: {
              'Authorization': 'Bearer ' + token,
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
          });
        } else {
          response = await fetch('/api/v1/organismes', {
            method: 'POST',
            headers: {
              'Authorization': 'Bearer ' + token,
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
          });
        }
        if (!response.ok) {
          alert("Erreur lors de l'enregistrement de l'organisme");
          return;
        }
        hideOrganismeModal();
        fetchAndRenderOrganismes();
      } catch (e) {
        alert('Erreur réseau');
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
        alert('Erreur lors de la suppression');
        hideDeleteOrganismeModal();
      }
    };
    </script>

  </section>
</main>

@include('includes.footer')
