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
  <div class="user-info">
    <span class="user-avatar" id="user-avatar"></span>
<span id="user-name"></span>
<script>
// Récupérer le nom de l'utilisateur depuis le localStorage (exemple: nom_utilisateur)
let nom = localStorage.nom_utilisateur || 'Super Admin';
let prenom = localStorage.prenom_utilisateur || '';
let fullName = (prenom + ' ' + nom).trim();
// if (!fullName) fullName = 'Admin';
// Générer l'avatar avec ui-avatars
const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random&length=2&color=ffffff&rounded=true`;
document.getElementById('user-avatar').innerHTML = `<img src="${avatarUrl}" alt="${fullName}" style="width:48px;height:48px;border-radius:50%;vertical-align:middle;">`;
document.getElementById('user-name').textContent = fullName;
</script>
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
