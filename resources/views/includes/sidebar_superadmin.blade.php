<nav class="sidebar" aria-label="Menu principal - Superadmin">
  <h2>MboaSignal - Superadmin</h2>
  <a href="{{ route('superadmin.dashboard') }}" class="{{ Request::is('superadmin/dashboard') ? 'active' : '' }}">Tableau de bord</a>
  <a href="{{ url('superadmin/utilisateurs') }}" class="{{ Request::is('superadmin/utilisateurs') ? 'active' : '' }}">Utilisateurs</a>
  <a href="{{ url('superadmin/organismes') }}" class="{{ Request::is('superadmin/organismes') ? 'active' : '' }}">Organismes</a>
  <a href="{{ url('superadmin/carte') }}" class="{{ Request::is('superadmin/carte') ? 'active' : '' }}">Carte</a>
  <a href="{{ url('superadmin/statistiques') }}" class="{{ Request::is('superadmin/statistiques') ? 'active' : '' }}">Statistiques</a>
  <a href="{{ url('superadmin/parametres') }}" class="{{ Request::is('superadmin/parametres') ? 'active' : '' }}">Paramètres</a>
</nav>
