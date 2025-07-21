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
        <span class="user-avatar">S</span>
        Superadmin
      </div>
    </header>

    <section>
      <h2>Liste des organismes</h2>

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
          <tbody>
            <tr>
              <td>1</td>
              <td>ENEO</td>
              <td>Énergie</td>
              <td>INkague.tech@eneo.cm</td>
              <td>53</td>
              <td><span class="status active">Actif</span></td>
              <td>
                <button class="btn-sm edit">Modifier</button>
                <button class="btn-sm delete">Supprimer</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>CAMWATER</td>
              <td>Eau</td>
              <td>Iby.tech@camwater.cm</td>
              <td>27</td>
              <td><span class="status inactive">Inactif</span></td>
              <td>
                <button class="btn-sm edit">Modifier</button>
                <button class="btn-sm delete">Supprimer</button>
              </td>
            </tr>
             <tr>
              <td>2</td>
              <td>SECURITE</td>
              <td>Police Municipale</td>
              <td>Mpabot.securite@mindef.cm</td>
              <td>27</td>
              <td><span class="status inactive">Inactif</span></td>
              <td>
                <button class="btn-sm edit">Modifier</button>
                <button class="btn-sm delete">Supprimer</button>
              </td>
            </tr>
            <!-- Ajouter d'autres lignes fictives ici -->
          </tbody>
        </table>
      </div>
    </section>

</main>

@include('includes.footer')
