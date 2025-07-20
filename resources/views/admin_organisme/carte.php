<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar_admin.php'; ?>

<main class="content">

    <header class="page-header">
      <h1>Carte des signalements</h1>
      <div class="user-info">
        <span class="user-avatar">A</span>
        Admin organisme
      </div>
    </header>

    <section>
        <div class="filter-buttons">
            <button class="filter-btn active" data-status="tous">Tous</button>
            <button class="filter-btn" data-status="en attente">En attente</button>
            <button class="filter-btn" data-status="en cours">En cours</button>
            <button class="filter-btn" data-status="résolu">Résolus</button>
        </div>

        <div id="map" style="height: 520px; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-top: 1rem;"></div>
    </section>

</main>

<?php include '../includes/footer.php'; ?>

<!-- Leaflet CSS + JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialisation de la carte
const map = L.map('map').setView([6.5, 12.5], 7.2); // Centré sur le Cameroun

L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap',
  maxZoom: 18
}).addTo(map);

// Signalements fictifs pour l'entité (ex : ENEO)
const signalements = [
  { nom: "Coupure générale - Zone A", lat: 7.37, lng: 13.55, statut: "en attente" },
  { nom: "Panne transformateur - Zone B", lat: 6.05, lng: 10.1, statut: "en cours" },
  { nom: "Travaux terminés - Zone C", lat: 4.05, lng: 9.7, statut: "résolu" },
  { nom: "Alarme secteur D", lat: 5.03, lng: 11.52, statut: "en attente" },
  { nom: "Rétablissement effectué", lat: 9.29, lng: 13.39, statut: "résolu" }
];

// Groupe de marqueurs
let markerGroup = L.layerGroup().addTo(map);

// Fonction d'affichage des marqueurs
function afficherMarqueurs(filtre) {
  markerGroup.clearLayers();

  signalements.forEach(sig => {
    if (filtre === "tous" || sig.statut === filtre) {
      const color = sig.statut === "résolu" ? "green"
                  : sig.statut === "en cours" ? "orange"
                  : "red";

      const marker = L.circleMarker([sig.lat, sig.lng], {
        radius: 10,
        fillColor: color,
        color: "#fff",
        weight: 2,
        opacity: 1,
        fillOpacity: 0.9
      }).bindPopup(`<strong>${sig.nom}</strong><br>Statut : <b>${sig.statut}</b>`);

      markerGroup.addLayer(marker);
    }
  });
}

// Affichage initial
afficherMarqueurs("tous");

// Gestion des boutons de filtre
document.querySelectorAll(".filter-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    const statut = btn.dataset.status;
    afficherMarqueurs(statut);
  });
});
</script>
