@include('includes.header')
<script>
if (!localStorage.type_utilisateur || localStorage.type_utilisateur !== 'admin') {
    window.location.href = '/unauthorized';
}
</script>
@include('includes.sidebar_admin')

<main class="content">

  <header class="page-header">
      <h1>Statistique - Mon organisme</h1>
      <div class="user-info">
        <span class="user-avatar">A</span>
        Admin organisme
      </div>
    </header>

  <section class="content-inner">
    <div style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between;">

      <div class="stat-card">
        <h2>Signalements totaux</h2>
        <p class="stat-value">124</p>
        <span class="stat-icon" style="background: var(--primary);"><i class="fas fa-bolt"></i></span>
      </div>

      <div class="stat-card">
        <h2>Signalements résolus</h2>
        <p class="stat-value">85</p>
        <span class="stat-icon" style="background: green;"><i class="fas fa-check-circle"></i></span>
      </div>

      <div class="stat-card">
        <h2>En cours de traitement</h2>
        <p class="stat-value">27</p>
        <span class="stat-icon" style="background: orange;"><i class="fas fa-spinner"></i></span>
      </div>

      <div class="stat-card">
        <h2>En attente</h2>
        <p class="stat-value">12</p>
        <span class="stat-icon" style="background: red;"><i class="fas fa-clock"></i></span>
      </div>

    </div>
  </section>

</main>

@include('includes.footer')

<!-- FontAwesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
  .stat-card {
    flex: 1 1 calc(25% - 1rem);
    background: rgba(255, 255, 255, 0.85);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;
    min-width: 220px;
  }

  .stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
  }

  .stat-card h2 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
  }

  .stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
  }

  .stat-icon {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 60px;
    height: 60px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    opacity: 0.9;
  }

  @media (max-width: 768px) {
    .stat-card {
      flex: 1 1 100%;
    }
  }
</style>
