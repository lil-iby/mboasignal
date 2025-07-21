@include('includes.header')
<script>
if (!localStorage.type_utilisateur || localStorage.type_utilisateur !== 'admin') {
    window.location.href = '/unauthorized';
}
</script>
@include('includes.sidebar_admin')


<main class="content">
 
    <header class="page-header">
      <h1>Liste des signalements</h1>
      <div class="user-info">
        <span class="user-avatar" id="user-avatar"></span>
<span id="user-name"></span>
<script>
  // Récupérer le nom de l'utilisateur depuis le localStorage (exemple: nom_utilisateur)
  let nom = localStorage.nom_utilisateur || 'Admin';
  let prenom = localStorage.prenom_utilisateur || '';
  let fullName = (prenom + ' ' + nom).trim();
  // if (!fullName) fullName = 'Admin';
  // Générer l'avatar avec ui-avatars
  const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random&length=2&color=ffffff&rounded=true`;
  document.getElementById('user-avatar').innerHTML = `<img src="${avatarUrl}" alt="${fullName}" style="width:48px;height:48px;border-radius:50%;vertical-align:middle;">`;
  document.getElementById('user-name').textContent = fullName;
</script>
      </div>
    </header>

    <section class="table-section">
      <table class="signalements-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Catégorie</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Données fictives pour test (à remplacer par requête BDD)
          $signalements = [
            ['id' => 101, 'titre' => 'Coupure électrique', 'date' => '2025-07-10', 'statut' => 'en attente', 'categorie' => 'Énergie'],
            ['id' => 102, 'titre' => 'Cable electrique couper', 'date' => '2025-07-12', 'statut' => 'en cours', 'categorie' => 'Énergie'],
            ['id' => 103, 'titre' => 'Poteau eletrique bruler', 'date' => '2025-07-08', 'statut' => 'résolu', 'categorie' => 'Énergie'],
          ];

          foreach ($signalements as $sig) {
            // Définir classe CSS selon statut
            $statusClass = '';
            if ($sig['statut'] === 'en attente') $statusClass = 'status-pending';
            elseif ($sig['statut'] === 'en cours') $statusClass = 'status-progress';
            elseif ($sig['statut'] === 'résolu') $statusClass = 'status-resolved';

            echo "<tr>";
            echo "<td>{$sig['id']}</td>";
            echo "<td>" . htmlspecialchars($sig['titre']) . "</td>";
            echo "<td>{$sig['date']}</td>";
            echo "<td><span class='status-badge $statusClass'>" . ucfirst($sig['statut']) . "</span></td>";
            echo "<td>" . htmlspecialchars($sig['categorie']) . "</td>";
            echo "<td>
                    <button class='btn action-view' title='Voir détails'>&#128065;</button>
                 </td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
<!-- Modale détails signalement -->
<div id="modalDetails" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h2>Détails du signalement</h2>
    <div id="modal-body">
      <!-- Contenu dynamique ici -->
      <p><strong>ID :</strong> <span id="modal-id"></span></p>
      <p><strong>Titre :</strong> <span id="modal-titre"></span></p>
      <p><strong>Date :</strong> <span id="modal-date"></span></p>
      <p><strong>Statut :</strong> <span id="modal-statut"></span></p>
      <p><strong>Catégorie :</strong> <span id="modal-categorie"></span></p>
      <p><strong>Description :</strong></p>
      <p id="modal-description">Description détaillée ici...</p>
    </div>
  </div>
</div>

</main>
<script>
  // Données fictives étendues avec description (à remplacer par BDD plus tard)
  const signalementsData = {
    101: {
      id: 101,
      titre: 'Coupure électrique',
      date: '2025-07-10',
      statut: 'en attente',
      categorie: 'Énergie',
      description: 'Une coupure électrique a été signalée dans le quartier central.'
    },
    102: {
      id: 102,
      titre: 'Cable electrique couper',
      date: '2025-07-12',
      statut: 'en cours',
      categorie: 'Energie',
      description: 'Un cable defectueux détecté près de la place du marché.'
    },
    103: {
      id: 103,
      titre: 'Poteau eletrique bruler',
      date: '2025-07-08',
      statut: 'résolu',
      categorie: 'Energie',
      description: 'Réparation terminée sur la route principale.'
    }
  };

  // Sélecteurs
  const modal = document.getElementById('modalDetails');
  const closeModalBtn = document.getElementById('closeModal');

  // Champs modale
  const modalId = document.getElementById('modal-id');
  const modalTitre = document.getElementById('modal-titre');
  const modalDate = document.getElementById('modal-date');
  const modalStatut = document.getElementById('modal-statut');
  const modalCategorie = document.getElementById('modal-categorie');
  const modalDescription = document.getElementById('modal-description');

  // Fonction ouvrir modale
  function openModal(id) {
    const data = signalementsData[id];
    if (!data) return;

    modalId.textContent = data.id;
    modalTitre.textContent = data.titre;
    modalDate.textContent = data.date;
    modalStatut.textContent = data.statut.charAt(0).toUpperCase() + data.statut.slice(1);
    modalCategorie.textContent = data.categorie;
    modalDescription.textContent = data.description;

    modal.style.display = 'block';
  }

  // Fermer modale au clic sur X
  closeModalBtn.onclick = function() {
    modal.style.display = 'none';
  };

  // Fermer modale au clic hors contenu
  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };

  // Attacher event aux boutons "Voir détails"
  document.querySelectorAll('.action-view').forEach(btn => {
    btn.addEventListener('click', function() {
      const row = this.closest('tr');
      const id = row.querySelector('td:first-child').textContent;
      openModal(id);
    });
  });
</script>

@include('includes.footer')
