<script>
document.addEventListener('DOMContentLoaded', function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const type = localStorage.getItem('type_utilisateur');
  if (!type) {
    window.location.href = '/login';
    return;
  } else if (type !== 'superadmin') {
    window.location.href = '/unauthorized';
    return;
  }
  const btnAddUser = document.getElementById('btn-add-user');
  if (btnAddUser) {
    btnAddUser.style.display = 'inline-block';
    btnAddUser.onclick = function() {
      document.getElementById('createUtilisateurForm').reset();
      document.getElementById('createUtilisateurErrors').textContent = '';
      document.getElementById('createUtilisateurModal').style.display = 'flex';
    };
  }
  // Soumission création utilisateur
  document.getElementById('createUtilisateurForm').onsubmit = async function(e) {
    e.preventDefault();
    const token = localStorage.getItem('auth_token');
    const data = {
      prenom_utilisateur: document.getElementById('createPrenomUtilisateur').value,
      nom_utilisateur: document.getElementById('createNomUtilisateur').value,
      email_utilisateur: document.getElementById('createEmailUtilisateur').value,
      tel_utilisateur: document.getElementById('createTelUtilisateur').value,
      type_utilisateur: document.getElementById('createTypeUtilisateur').value,
      pass_utilisateur: document.getElementById('createPasswordUtilisateur').value,
      pass_utilisateur_confirmation: document.getElementById('createPasswordUtilisateurConfirm').value
    };
    const res = await fetch('/api/v1/utilisateurs', {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(data)
    });
    if (res.status === 422) {
      const err = await res.json();
      document.getElementById('createUtilisateurErrors').textContent = Object.values(err.errors).join(' ');
      return;
    }
    document.getElementById('createUtilisateurModal').style.display = 'none';
    fetchAndRenderUsers();
  };
});
</script>
<body>
@include('includes.header')
@include('includes.sidebar_superadmin')

<main class="content">
  <header class="page-header" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <h1>Gestion des utilisateurs</h1>
    </div>
    <div class="user-info">
      <span class="user-avatar" id="user-avatar"></span>
      <span id="user-name"></span>
    </div>
  </header>

  <script>
    const nom = localStorage.nom_utilisateur || 'Super Admin';
    const prenom = localStorage.prenom_utilisateur || '';
    const fullName = (prenom + ' ' + nom).trim();
    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random&length=2&color=ffffff&rounded=true`;
    document.getElementById('user-avatar').innerHTML = `<img src="${avatarUrl}" alt="${fullName}" style="width:48px;height:48px;border-radius:50%;vertical-align:middle;">`;
    document.getElementById('user-name').textContent = fullName;
  </script>
  
  <section>
    <h2>Liste des utilisateurs</h2>
    <button id="btn-add-user-table" title="Créer un utilisateur" style="background:#007bff; color:#fff; border:none; border-radius:5px; padding:10px 15px; font-size:1rem; cursor:pointer;">
      + Créer un utilisateur
    </button>
    <div class="table-wrapper">
      <table class="styled-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="users-table-body">
          <tr><td colspan="6">Chargement...</td></tr>
        </tbody>
      </table>
    </div>
  </section>
@include('superadmin.partials.confirm_statut_utilisateur_modal')
@include('superadmin.partials.confirm_statut_utilisateur_modal_js')
@include('superadmin.partials.edit_utilisateur_modal')
@include('superadmin.partials.create_utilisateur_modal')
</main>

<!-- Modals -->
<div id="deleteModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:2rem;border-radius:8px;max-width:350px;margin:auto;text-align:center;">
    <h3>Supprimer l'utilisateur ?</h3>
    <p>Cette action est irréversible.</p>
    <button id="confirmDeleteBtn" class="btn btn-danger">Supprimer</button>
    <button onclick="hideDeleteModal()" class="btn">Annuler</button>
  </div>
</div>

<div id="statusModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:2rem;border-radius:8px;max-width:350px;margin:auto;text-align:center;">
    <h3 id="statusModalTitle"></h3>
    <p id="statusModalText"></p>
    <button id="confirmStatusBtn" class="btn btn-primary">Confirmer</button>
    <button onclick="hideStatusModal()" class="btn">Annuler</button>
  </div>
</div>

<script>
// --- MODALE CRÉATION UTILISATEUR ---
document.addEventListener('DOMContentLoaded', function() {
  // Ouvre la modale à l'ouverture du bouton
  const btnAddUser = document.getElementById('btn-add-user-table');
  const modal = document.getElementById('createUtilisateurModal');
  const closeBtn = document.getElementById('closeCreateUtilisateurModal');
  const form = document.getElementById('createUtilisateurForm');
  const errors = document.getElementById('createUtilisateurErrors');
  if (btnAddUser && modal && closeBtn && form) {
    btnAddUser.onclick = function() {
      form.reset();
      errors.textContent = '';
      modal.style.display = 'flex';
    };
    closeBtn.onclick = function() {
      modal.style.display = 'none';
    };
    // Ferme la modale si clic hors contenu
    modal.onclick = function(e) {
      if (e.target === modal) modal.style.display = 'none';
    };
    // Soumission du formulaire
    form.onsubmit = async function(e) {
      e.preventDefault();
      errors.textContent = '';
      const token = localStorage.getItem('auth_token');
      const data = {
        prenom_utilisateur: document.getElementById('createPrenomUtilisateur').value,
        nom_utilisateur: document.getElementById('createNomUtilisateur').value,
        email_utilisateur: document.getElementById('createEmailUtilisateur').value,
        tel_utilisateur: document.getElementById('createTelUtilisateur').value,
        type_utilisateur: document.getElementById('createTypeUtilisateur').value,
        pass_utilisateur: document.getElementById('createPasswordUtilisateur').value,
        pass_utilisateur_confirmation: document.getElementById('createPasswordUtilisateurConfirm').value
      };
      const res = await fetch('/api/v1/utilisateurs', {
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
      if (res.status === 422) {
        const err = await res.json();
        errors.textContent = Object.values(err.errors).join(' ');
        return;
      }
      modal.style.display = 'none';
      fetchAndRenderUsers();
    };
  }
});

// --- FIN MODALE CRÉATION UTILISATEUR ---

async function fetchAndRenderUsers() {
    const token = localStorage.getItem('auth_token');
    const idOrganisme = localStorage.getItem('id_organisme');
    const tableBody = document.getElementById('users-table-body');
    tableBody.innerHTML = '<tr><td colspan="6">Chargement...</td></tr>';
    try {
      const response = await fetch('/api/v1/utilisateurs', {
        headers: {
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json'
        }
      });
      if (!response.ok) {
        tableBody.innerHTML = '<tr><td colspan="6">Erreur lors du chargement</td></tr>';
        return;
      }
      const users = await response.json();
      const filtered = users.filter(u => u.id_organisme == idOrganisme && u.type_utilisateur !== 'admin');
      if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6">Aucun utilisateur trouvé</td></tr>';
        return;
      }
      tableBody.innerHTML = filtered.map((u, i) => `
        <tr>
          <td>${i + 1}</td>
          <td>${u.prenom_utilisateur} ${u.nom_utilisateur}</td>
          <td>${u.email_utilisateur}</td>
          <td>${u.type_utilisateur}</td>
          <td><span class="status" style="color:${u.etat_compte === 'désactivé' ? 'red' : 'green'};font-weight:bold;">${u.etat_compte === 'désactivé' ? 'Désactivé' : 'Activé'}</span></td>
          <td>
            <button class="btn-sm status" data-id="${u.id_utilisateur}" data-status="${u.etat_compte}">${u.etat_compte === 'activé' ? 'Désactiver' : 'Activer'}</button>
<button class="btn-sm edit" data-id="${u.id_utilisateur}">Modifier</button>
          </td>
        </tr>`).join('');

      document.querySelectorAll('.btn-sm.edit').forEach(btn => {
        btn.onclick = async function() {
          const userId = this.dataset.id;
          const token = localStorage.getItem('auth_token');
          // Récupérer les données utilisateur pour pré-remplir le formulaire
          const res = await fetch(`/api/v1/utilisateurs/${userId}`, {
            headers: {
              'Authorization': 'Bearer ' + token,
              'Accept': 'application/json'
            }
          });
          if (!res.ok) return;
          const user = await res.json();
          document.getElementById('editUtilisateurId').value = user.id_utilisateur;
          document.getElementById('editPrenomUtilisateur').value = user.prenom_utilisateur || '';
          document.getElementById('editNomUtilisateur').value = user.nom_utilisateur || '';
          document.getElementById('editEmailUtilisateur').value = user.email_utilisateur || '';
          document.getElementById('editTelUtilisateur').value = user.tel_utilisateur || '';
          document.getElementById('editTypeUtilisateur').value = user.type_utilisateur || 'utilisateur';
          document.getElementById('editUtilisateurErrors').textContent = '';
          document.getElementById('editUtilisateurModal').style.display = 'flex';
        };
      });

      // Soumission du formulaire d'édition utilisateur
      const editForm = document.getElementById('editUtilisateurForm');
      editForm.onsubmit = async function(e) {
        e.preventDefault();
        const userId = document.getElementById('editUtilisateurId').value;
        const token = localStorage.getItem('auth_token');
        const data = {
          prenom_utilisateur: document.getElementById('editPrenomUtilisateur').value,
          nom_utilisateur: document.getElementById('editNomUtilisateur').value,
          email_utilisateur: document.getElementById('editEmailUtilisateur').value,
          tel_utilisateur: document.getElementById('editTelUtilisateur').value,
          type_utilisateur: document.getElementById('editTypeUtilisateur').value
        };
        const res = await fetch(`/api/v1/utilisateurs/${userId}`, {
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
        if (res.status === 422) {
          const err = await res.json();
          document.getElementById('editUtilisateurErrors').textContent = Object.values(err.errors).join(' ');
          return;
        }
        document.getElementById('editUtilisateurModal').style.display = 'none';
        fetchAndRenderUsers();
      };
      document.getElementById('btnCancelEditUtilisateur').onclick = function() {
        document.getElementById('editUtilisateurModal').style.display = 'none';
      };


      document.querySelectorAll('.btn-sm.delete').forEach(btn => {
        btn.onclick = () => showDeleteModal(btn.dataset.id);
      });
      document.querySelectorAll('.btn-sm.status').forEach(btn => {
        btn.onclick = function() {
          const userId = this.dataset.id;
          const currentStatus = this.dataset.status;
          const action = currentStatus === 'activé' ? 'disable' : 'enable';
          const confirmMsg = action === 'disable'
            ? "Voulez-vous vraiment désactiver ce compte utilisateur ?"
            : "Voulez-vous vraiment activer ce compte utilisateur ?";
          showStatutUtilisateurConfirmModal({
            id: userId,
            action,
            confirmMsg,
            onConfirm: async function() {
              const token = localStorage.getItem('auth_token');
              await fetch(`/api/v1/utilisateurs/${userId}`, {
                method: 'PATCH',
                headers: {
                  'Authorization': 'Bearer ' + token,
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ etat_compte: action === 'disable' ? 'désactivé' : 'activé' })
              });
              await fetchAndRenderUsers();
            }
          });
        };
      });
    } catch (e) {
      tableBody.innerHTML = '<tr><td colspan="6">Erreur réseau</td></tr>';
    }
  }

  let deleteUserId = null;
  function showDeleteModal(userId) {
    deleteUserId = userId;
    document.getElementById('deleteModal').style.display = 'flex';
  }
  function hideDeleteModal() {
    deleteUserId = null;
    document.getElementById('deleteModal').style.display = 'none';
  }
  document.getElementById('confirmDeleteBtn').onclick = async function() {
    const token = localStorage.getItem('auth_token');
    await fetch(`/api/v1/utilisateurs/${deleteUserId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    hideDeleteModal();
    fetchAndRenderUsers();
  };

  let statusUserId = null;
  let statusToSet = null;
  function showStatusModal(userId, currentStatus) {
    statusUserId = userId;
    statusToSet = currentStatus === 'activé' ? 'desactivé' : 'activé';
    document.getElementById('statusModalTitle').textContent = statusToSet === 'activé' ? 'Activer le compte ?' : 'Désactiver le compte ?';
    document.getElementById('statusModalText').textContent = statusToSet === 'activé' ? "L'utilisateur pourra se connecter." : "L'utilisateur ne pourra plus se connecter.";
    document.getElementById('statusModal').style.display = 'flex';
  }
  function hideStatusModal() {
    statusUserId = null;
    statusToSet = null;
    document.getElementById('statusModal').style.display = 'none';
  }
  document.getElementById('confirmStatusBtn').onclick = async function() {
    const token = localStorage.getItem('auth_token');
    await fetch(`/api/v1/utilisateurs/${statusUserId}`, {
      method: 'PATCH',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ etat_compte: statusToSet })
    });
    hideStatusModal();
    fetchAndRenderUsers();
  };

  document.addEventListener('DOMContentLoaded', fetchAndRenderUsers);
</script>

@include('includes.footer')