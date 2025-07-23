<nav class="sidebar" aria-label="Menu principal - Admin organisme">
  <h2>MboaSignal - Admin Organisme</h2>
  <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">Tableau de bord</a>
  <a href="{{ url('admin/signalements') }}" class="{{ Request::is('admin/signalements') ? 'active' : '' }}">Signalements</a>
  <a href="{{ url('admin/techniciens') }}" class="{{ Request::is('admin/techniciens') ? 'active' : '' }}">Techniciens</a>
  <a href="{{ url('admin/carte') }}" class="{{ Request::is('admin/carte') ? 'active' : '' }}">Carte</a>
  <a href="{{ url('admin/statistiques') }}" class="{{ Request::is('admin/statistiques') ? 'active' : '' }}">Statistiques</a>
  <a href="{{ url('admin/mon_organisme') }}" class="{{ Request::is('admin/mon_organisme') ? 'active' : '' }}">Mon organisme</a>
  <div class="sidebar-footer">
    <a href="#" id="logout-btn" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Déconnexion
    </a>
  </div>
</nav>

<script>
document.getElementById('logout-btn').addEventListener('click', function(e) {
  e.preventDefault();
  // Vider le localStorage
  localStorage.clear();
  // Rediriger vers la page de connexion
  window.location.href = '/login';
});
</script>