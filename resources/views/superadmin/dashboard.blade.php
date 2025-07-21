<script>
  // Vérification du rôle superadmin avant affichage
  if (localStorage.getItem('type_utilisateur') !== 'superadmin') {
    window.location.href = '/unauthorized';
  }
</script>
@include('includes.header')
@include('includes.sidebar_superadmin')
<main class="content" role="main">
  <header class="page-header">
  <h1>Tableau de bord</h1>
  <div class="user-info" aria-label="Informations utilisateur">
    <span class="user-avatar">S</span>
    Superadmin
    <a href="../logout.php" class="logout-btn" title="Se déconnecter" aria-label="Se déconnecter">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</header>

  <section class="stats-cards">
  <div class="stat-card stat-waiting">
    <h3>En attente</h3>
    <p class="count">42</p>
    <span>Signalements non traités</span>
  </div>

  <div class="stat-card stat-resolved">
    <h3>Résolus</h3>
    <p class="count">120</p>
    <span>Signalements clôturés</span>
  </div>

  <div class="stat-card stat-total">
    <h3>Total</h3>
    <p class="count">162</p>
    <span>Signalements enregistrés</span>
  </div>
</section>

</main>


@include('includes.footer')
