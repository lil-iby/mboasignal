<nav class="sidebar" aria-label="Menu principal - Admin organisme">
  <h2>MboaSignal - Admin Organisme</h2>
  <a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : '' ?>">Tableau de bord</a>
  <a href="signalements.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'signalements.php') ? 'active' : '' ?>">Signalements</a>
  <a href="techniciens.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'techniciens.php') ? 'active' : '' ?>">Techniciens</a>
  <a href="carte.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'carte.php') ? 'active' : '' ?>">Carte</a>
  <a href="statistiques.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'statistiques.php') ? 'active' : '' ?>">Statistiques</a>
  <a href="mon_organisme.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'mon_organisme.php') ? 'active' : '' ?>">Mon organisme</a>

</nav>
