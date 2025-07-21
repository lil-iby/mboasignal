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
      <h1>Statistiques globales</h1>
      <div class="user-info">
        <span class="user-avatar">S</span>
        Superadmin
      </div>
    </header>

    <section class="stats-grid">
      <div class="stat-box">
        <h3>Total signalements</h3>
        <p class="big-number">152</p>
      </div>
      <div class="stat-box">
        <h3>Signalements résolus</h3>
        <p class="big-number">96</p>
      </div>
      <div class="stat-box">
        <h3>Utilisateurs enregistrés</h3>
        <p class="big-number">43</p>
      </div>
      <div class="stat-box">
        <h3>Organismes partenaires</h3>
        <p class="big-number">5</p>
      </div>
    </section>

</main>

@include('includes.footer')
