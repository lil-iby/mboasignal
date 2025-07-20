<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar_superadmin.php'; ?>

<main class="content">
  <div class="content-inner">
    <header class="page-header">
      <h1>Paramètres du compte</h1>
      <div class="user-info">
        <span class="user-avatar">S</span>
        Superadmin
      </div>
    </header>

    <section class="settings-section">
      <div class="settings-box">
        <h3>Informations personnelles</h3>
        <form>
          <div class="form-group">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" value="Superadmin">
          </div>

          <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" value="superadmin@email.com">
          </div>

          <div class="form-group">
            <label for="role">Rôle</label>
            <input type="text" id="role" value="Super administrateur" disabled>
          </div>

          <button type="submit" class="btn-update" disabled>Modifier </button>
        </form>
      </div>
    </section>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
