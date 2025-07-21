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
      <h1>Gestion des utilisateurs</h1>
      <div class="user-info">
        <span class="user-avatar">S</span>
        Superadmin
      </div>
    </header>

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
          <tbody>
            <tr>
              <td>1</td>
              <td>Erinn Mpabot</td>
              <td>erinn.mpabot@gmail.com</td>
              <td>Admin Organisme</td>
              <td><span class="status active">Actif</span></td>
              <td>
                <button class="btn-sm edit">Modifier</button>
                <button class="btn-sm delete">Supprimer</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Alice Mba</td>
              <td>alice.mba@gmail.com</td>
              <td>Technicien</td>
              <td><span class="status inactive">Inactif</span></td>
              <td>
                <button class="btn-sm edit">Modifier</button>
                <button class="btn-sm delete">Supprimer</button>
              </td>
            </tr>
            <!-- Plus d'utilisateurs... -->
          </tbody>
        </table>
      </div>
    </section>

</main>

@include('includes.footer')
