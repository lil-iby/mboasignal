<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const type = localStorage.getItem('type_utilisateur');
  if (!type) {
    window.location.href = '/login';
  } else if (type !== 'superadmin') {
    window.location.href = '/unauthorized';
  }
</script>
<body>
@include('includes.header')
@include('includes.sidebar_superadmin')

<main class="content">
  <header class="page-header">
    <h1>Gestion des utilisateurs</h1>
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
          <td><span class="status ${u.etat_compte === 'actif' ? 'active' : 'inactive'}">${u.etat_compte}</span></td>
          <td>
            <button class="btn-sm status" data-id="${u.id_utilisateur}" data-status="${u.etat_compte}">${u.etat_compte === 'actif' ? 'Désactiver' : 'Activer'}</button>
            <button class="btn-sm delete" data-id="${u.id_utilisateur}">Supprimer</button>
          </td>
        </tr>`).join('');

      document.querySelectorAll('.btn-sm.delete').forEach(btn => {
        btn.onclick = () => showDeleteModal(btn.dataset.id);
      });
      document.querySelectorAll('.btn-sm.status').forEach(btn => {
        btn.onclick = () => showStatusModal(btn.dataset.id, btn.dataset.status);
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
    statusToSet = currentStatus === 'actif' ? 'inactif' : 'actif';
    document.getElementById('statusModalTitle').textContent = statusToSet === 'actif' ? 'Activer le compte ?' : 'Désactiver le compte ?';
    document.getElementById('statusModalText').textContent = statusToSet === 'actif' ? "L'utilisateur pourra se connecter." : "L'utilisateur ne pourra plus se connecter.";
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