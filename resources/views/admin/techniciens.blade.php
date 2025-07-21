@include('includes.header')
<script>
if (!localStorage.type_utilisateur || localStorage.type_utilisateur !== 'admin') {
    window.location.href = '/unauthorized';
}
</script>
@include('includes.sidebar_admin')



<main class="content">
    <header class="page-header">
      <h1>Techniciens - ENEO Cameroun</h1>
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
      <button id="btnAddTech" class="btn btn-primary" style="margin-bottom: 1rem;">Ajouter un technicien</button>
      <table class="techniciens-table">
        <thead>
          <tr>
            <th>Nom complet</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Données fictives
          $techniciens = [
            ['id' => 1, 'nom' => 'Jean Mbassi', 'email' => 'jean.mbassi@eneo.cm', 'tel' => '699123456', 'statut' => 'actif'],
            ['id' => 2, 'nom' => 'Amina Douala', 'email' => 'amina.douala@eneo.cm', 'tel' => '697654321', 'statut' => 'actif'],
            ['id' => 3, 'nom' => 'Paul Tchou', 'email' => 'paul.tchou@eneo.cm', 'tel' => '696987654', 'statut' => 'inactif'],
          ];

          foreach ($techniciens as $tech) {
            $statusClass = ($tech['statut'] === 'actif') ? 'status-active' : 'status-inactive';

            echo "<tr>";
            echo "<td>" . htmlspecialchars($tech['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($tech['email']) . "</td>";
            echo "<td>" . htmlspecialchars($tech['tel']) . "</td>";
            echo "<td><span class='status-badge $statusClass'>" . ucfirst($tech['statut']) . "</span></td>";
            echo "<td>
                    <button class='btn action-edit' title='Modifier' data-id='{$tech['id']}'>&#9998;</button>
                    <button class='btn action-delete' title='Supprimer' data-id='{$tech['id']}'>&#128465;</button>
                  </td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

</main>

@include('includes.footer')
