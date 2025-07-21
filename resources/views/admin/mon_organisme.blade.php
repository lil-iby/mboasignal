@include('includes.header')
<script>
if (!localStorage.type_utilisateur || localStorage.type_utilisateur !== 'admin') {
    window.location.href = '/unauthorized';
}
</script>
@include('includes.sidebar_admin')

<main class="content">

 <header class="page-header">
    <h1>Mon organisme</h1>
    <div class="user-info">
      <span class="user-avatar" id="user-avatar"></span>
<span id="user-name"></span>
<script>
// Récupérer le nom de l'utilisateur depuis le localStorage (exemple: nom_utilisateur)
let nom = localStorage.nom_utilisateur || 'Admin';
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
  <section class="content">

    <div class="organisme-card">
      <div class="organisme-header">
        <div class="organisme-logo">
          <i class="fas fa-building"></i>
        </div>
        <div class="organisme-info">
          <h2>ENEO Cameroun</h2>
          <p class="sector">Secteur : Énergie</p>
        </div>
      </div>

      <div class="organisme-details">
        <p><strong>Email :</strong> contact@eneo.cm</p>
        <p><strong>Téléphone :</strong> +237 699 00 00 00</p>
        <p><strong>Adresse :</strong> Yaoundé, quartier administratif</p>
        <p><strong>Description :</strong> ENEO est le fournisseur principal d'électricité au Cameroun. Il assure la production, le transport et la distribution de l’énergie sur l’ensemble du territoire.</p>
      </div>

      <div class="organisme-actions">
       <button class="btn-edit" onclick="openModal()"><i class="fas fa-edit"></i> Modifier</button>

      </div>
    </div>

  </section>
<!-- ✅ MODAL à ajouter -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Modifier les informations</h2>
    <form>
      <label>Nom :</label>
      <input type="text" value="ENEO Cameroun" />

      <label>Secteur :</label>
      <input type="text" value="Énergie" />

      <label>Email :</label>
      <input type="email" value="contact@eneo.cm" />

      <label>Téléphone :</label>
      <input type="text" value="+237 699 00 00 00" />

      <label>Adresse :</label>
      <input type="text" value="Yaoundé, quartier administratif" />

      <label>Description :</label>
      <textarea rows="4">ENEO est le fournisseur principal d'électricité au Cameroun. Il assure la production, le transport et la distribution de l’énergie sur l’ensemble du territoire.</textarea>

      <button type="submit">Enregistrer</button>
    </form>
  </div>
</div>
</main>

@include('includes.footer')

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.organisme-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  max-width: 800px;
  margin: 0 auto;
  animation: fadeIn 0.4s ease;
}

.organisme-header {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 1rem;
}

.organisme-logo {
  background: var(--primary);
  color: white;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  box-shadow: 0 0 16px rgba(30,144,255,0.3);
}

.organisme-info h2 {
  margin: 0;
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--primary-dark);
}

.organisme-info .sector {
  font-size: 1rem;
  color: var(--text-dark);
}

.organisme-details p {
  margin: 0.4rem 0;
  font-size: 1rem;
  color: var(--text-dark);
}

.organisme-actions {
  margin-top: 1.8rem;
  text-align: right;
}

.btn-edit {
  background: var(--primary);
  color: white;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 14px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(30,144,255,0.3);
  transition: background 0.3s ease;
}

.btn-edit:hover {
  background: var(--primary-dark);
}

@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
.logout-btn {
  color: white;
  background: rgba(255,255,255,0.15);
  padding: 0.4rem 0.6rem;
  border-radius: 10px;
  margin-left: 1rem;
  transition: background 0.3s ease;
  text-decoration: none;
}
.logout-btn:hover {
  background: rgba(255,255,255,0.3);
}

/* MODAL */
.modal {
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
}

.modal-content {
  margin-bottom: 25px; /* ✨ Ajout important */
 
  background: white;
  padding: 2rem;
  border-radius: 14px;
  width: 90%;
  max-width: 600px;
  animation: slideDown 0.3s ease-out;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
}

.modal-content h2 {
    margin-top:0px;
  margin-bottom: 1.2rem;
  color: var(--primary-dark);
}

.modal-content form {
  display: flex;
  flex-direction: column;
}

.modal-content form label {
  margin: 0.8rem 0 0.3rem;
  font-weight: 600;
  color: var(--text-dark);
}

.modal-content form input,
.modal-content form textarea {
  padding: 0.6rem;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-family: 'Poppins', sans-serif;
}

.modal-content form button {
  margin-top: 1.5rem;
  background: var(--primary);
  color: white;
  padding: 0.7rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.modal-content form button:hover {
  background: var(--primary-dark);
}

.close {
  position: absolute;
  top: 1rem;
  right: 1.2rem;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-dark);
}

@keyframes slideDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>

<script>
function openModal() {
  document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('editModal').style.display = 'none';
}
</script>