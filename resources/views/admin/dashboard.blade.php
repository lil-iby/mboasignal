@include('includes.header')
<script>
if (!localStorage.type_utilisateur || localStorage.type_utilisateur !== 'admin') {
    window.location.href = '/unauthorized';
}
</script>
@include('includes.sidebar_admin')

<main class="content" role="main">
 
    <header class="page-header">
  <h1>Tableau de bord</h1>
  <div class="user-info" aria-label="Informations utilisateur">
    <span class="user-avatar">A</span>
    Admin organisme
    <a href="../logout.php" class="logout-btn" title="Se déconnecter" aria-label="Se déconnecter">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</header>

    <section class="stats-cards">
  <div class="stat-card stat-waiting">
    <h3>En attente</h3>
    <p class="count">18</p>
    <span>Signalements en attente pour ENEO</span>
  </div>

  <div class="stat-card stat-resolved">
    <h3>Résolus</h3>
    <p class="count">35</p>
    <span>Signalements traités par ENEO</span>
  </div>

  <div class="stat-card stat-total">
    <h3>Total</h3>
    <p class="count">53</p>
    <span>Signalements reçus par ENEO</span>
  </div>
</section>

</main>

@include('includes.footer')
