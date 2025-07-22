<style>
#createUtilisateurModal.modal {
  display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.4); align-items: center; justify-content: center;
}
#createUtilisateurModal .modal-content {
  background: #fff; padding: 2.5rem 2rem 2rem 2rem; border-radius: 12px;
  max-width: 95vw; min-width: 320px; width: 100%; max-width: 420px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  position: relative; text-align: left; animation: fadeInModal .25s;
}
#createUtilisateurModal .modal-content h4 {
  margin-top: 0; margin-bottom: 1.5rem; font-size: 1.5rem; color: #2a2a2a; text-align: center;
}
#createUtilisateurModal label {
  font-weight: 500; color: #333; margin-bottom: 0.2rem; display: block;
}
#createUtilisateurModal input, #createUtilisateurModal select {
  width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid #cfd8dc;
  margin-bottom: 1.1rem; font-size: 1rem; background: #f8fafc; transition: border 0.2s;
}
#createUtilisateurModal input:focus, #createUtilisateurModal select:focus {
  border: 1.5px solid #007bff; outline: none; background: #fff;
}
#createUtilisateurModal .btn {
  padding: 0.5rem 1.2rem; border-radius: 5px; border: none; font-weight: 500; font-size: 1rem; cursor: pointer;
  transition: background 0.18s;
}
#createUtilisateurModal .btn-success {
  background: #007bff; color: #fff;
}
#createUtilisateurModal .btn-success:hover {
  background: #0056b3;
}
#createUtilisateurModal .btn-secondary {
  background: #e0e0e0; color: #333;
}
#createUtilisateurModal .btn-secondary:hover {
  background: #bdbdbd;
}
#createUtilisateurModal .close-modal {
  position: absolute; top: 12px; right: 18px; font-size: 1.5rem; color: #888; background: none; border: none; cursor: pointer;
}
#createUtilisateurModal #createUtilisateurErrors {
  color: #c82333; font-size: 0.97rem; margin-bottom: 1rem; min-height: 1.2em;
}
@media (max-width: 480px) {
  #createUtilisateurModal .modal-content { padding: 1.1rem 0.5rem; min-width: 0; }
}
@keyframes fadeInModal {
  from { transform: translateY(50px); opacity: 0; } to { transform: none; opacity: 1; }
}
</style>
<div id="createUtilisateurModal" class="modal">
  <div class="modal-content">
    <button type="button" class="close-modal" id="closeCreateUtilisateurModal" title="Fermer">&times;</button>
    <h4>Créer un utilisateur</h4>
    <form id="createUtilisateurForm" autocomplete="off">
      <label for="createPrenomUtilisateur">Prénom</label>
      <input type="text" id="createPrenomUtilisateur" class="form-control" required placeholder="Prénom">
      <label for="createNomUtilisateur">Nom</label>
      <input type="text" id="createNomUtilisateur" class="form-control" required placeholder="Nom">
      <label for="createEmailUtilisateur">Email</label>
      <input type="email" id="createEmailUtilisateur" class="form-control" required placeholder="Adresse email">
      <label for="createTelUtilisateur">Téléphone</label>
      <input type="text" id="createTelUtilisateur" class="form-control" placeholder="Numéro de téléphone">
      <label for="createTypeUtilisateur">Type</label>
      <select id="createTypeUtilisateur" class="form-control">
        <option value="utilisateur">Utilisateur</option>
        <option value="moderateur">Modérateur</option>
        <option value="admin">Admin</option>
      </select>
      <label for="createPasswordUtilisateur">Mot de passe</label>
      <input type="password" id="createPasswordUtilisateur" class="form-control" required placeholder="Mot de passe">
      <label for="createPasswordUtilisateurConfirm">Confirmer le mot de passe</label>
      <input type="password" id="createPasswordUtilisateurConfirm" class="form-control" required placeholder="Confirmer le mot de passe">
      <div id="createUtilisateurErrors"></div>
      <div style="text-align:right;margin-top:1.7rem;">
        <button type="submit" class="btn btn-success">Créer</button>
        <button type="button" class="btn btn-secondary" id="btnCancelCreateUtilisateur" style="margin-left:1rem;">Annuler</button>
      </div>
    </form>
  </div>
</div>
<script>
(function(){
  const modal = document.getElementById('createUtilisateurModal');
  document.getElementById('closeCreateUtilisateurModal').onclick = function() { modal.style.display = 'none'; };
  document.getElementById('btnCancelCreateUtilisateur').onclick = function() { modal.style.display = 'none'; };
  window.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
})();
</script>
