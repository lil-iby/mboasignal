<style>
#editUtilisateurModal.modal {
  display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.4); align-items: center; justify-content: center;
}
#editUtilisateurModal .modal-content {
  background: #fff; padding: 2.5rem 2rem 2rem 2rem; border-radius: 12px;
  max-width: 95vw; min-width: 320px; width: 100%; max-width: 420px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  position: relative; text-align: left; animation: fadeInModal .25s;
}
#editUtilisateurModal .modal-content h4 {
  margin-top: 0; margin-bottom: 1.5rem; font-size: 1.5rem; color: #2a2a2a; text-align: center;
}
#editUtilisateurModal label {
  font-weight: 500; color: #333; margin-bottom: 0.2rem; display: block;
}
#editUtilisateurModal input, #editUtilisateurModal select {
  width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid #cfd8dc;
  margin-bottom: 1.1rem; font-size: 1rem; background: #f8fafc; transition: border 0.2s;
}
#editUtilisateurModal input:focus, #editUtilisateurModal select:focus {
  border: 1.5px solid #007bff; outline: none; background: #fff;
}
#editUtilisateurModal .btn {
  padding: 0.5rem 1.2rem; border-radius: 5px; border: none; font-weight: 500; font-size: 1rem; cursor: pointer;
  transition: background 0.18s;
}
#editUtilisateurModal .btn-success {
  background: #28a745; color: #fff;
}
#editUtilisateurModal .btn-success:hover {
  background: #218838;
}
#editUtilisateurModal .btn-secondary {
  background: #e0e0e0; color: #333;
}
#editUtilisateurModal .btn-secondary:hover {
  background: #bdbdbd;
}
#editUtilisateurModal .close-modal {
  position: absolute; top: 12px; right: 18px; font-size: 1.5rem; color: #888; background: none; border: none; cursor: pointer;
}
#editUtilisateurModal #editUtilisateurErrors {
  color: #c82333; font-size: 0.97rem; margin-bottom: 1rem; min-height: 1.2em;
}
@media (max-width: 480px) {
  #editUtilisateurModal .modal-content { padding: 1.1rem 0.5rem; min-width: 0; }
}
@keyframes fadeInModal {
  from { transform: translateY(50px); opacity: 0; } to { transform: none; opacity: 1; }
}
</style>
<div id="editUtilisateurModal" class="modal">
  <div class="modal-content">
    <button type="button" class="close-modal" id="closeEditUtilisateurModal" title="Fermer">&times;</button>
    <h4>Modifier l'utilisateur</h4>
    <form id="editUtilisateurForm" autocomplete="off">
      <input type="hidden" id="editUtilisateurId">
      <label for="editPrenomUtilisateur">Prénom</label>
      <input type="text" id="editPrenomUtilisateur" class="form-control" required placeholder="Prénom">
      <label for="editNomUtilisateur">Nom</label>
      <input type="text" id="editNomUtilisateur" class="form-control" required placeholder="Nom">
      <label for="editEmailUtilisateur">Email</label>
      <input type="email" id="editEmailUtilisateur" class="form-control" required placeholder="Adresse email">
      <label for="editTelUtilisateur">Téléphone</label>
      <input type="text" id="editTelUtilisateur" class="form-control" placeholder="Numéro de téléphone">
      <label for="editTypeUtilisateur">Type</label>
      <select id="editTypeUtilisateur" class="form-control">
        <option value="citoyen">Citoyen</option>
        <option value="technicien">Technicien</option>
        <option value="administrateur">Administrateur</option>
        <option value="superadmin">Super Admin</option>
      </select>
      <div id="editUtilisateurErrors"></div>
      <div style="text-align:right;margin-top:1.7rem;">
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <button type="button" class="btn btn-secondary" id="btnCancelEditUtilisateur" style="margin-left:1rem;">Annuler</button>
      </div>
    </form>
  </div>
</div>
<script>
// Fermer la modale avec la croix ou le clic hors contenu
(function(){
  const modal = document.getElementById('editUtilisateurModal');
  document.getElementById('closeEditUtilisateurModal').onclick = function() {
    modal.style.display = 'none';
  };
  document.getElementById('btnCancelEditUtilisateur').onclick = function() {
    modal.style.display = 'none';
  };
  window.addEventListener('click', function(e) {
    if (e.target === modal) modal.style.display = 'none';
  });
})();
</script>
