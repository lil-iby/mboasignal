<nav class="sidebar" aria-label="Menu principal - Superadmin">
  <h2>MboaSignal - Superadmin</h2>
  <a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : '' ?>">Tableau de bord</a>
  <a href="utilisateurs.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'utilisateurs.php') ? 'active' : '' ?>">Utilisateurs</a>
  <a href="organismes.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'organismes.php') ? 'active' : '' ?>">Organismes</a>
  <a href="carte.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'carte.php') ? 'active' : '' ?>">Carte</a>
  <a href="statistiques.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'statistiques.php') ? 'active' : '' ?>">Statistiques</a>
  <a href="paramètres.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'parametres.php') ? 'active' : '' ?>">Paramètres</a>
</nav>
