<div id="editUtilisateurModal" class="modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
  <div class="modal-content" style="background:#fff;padding:2rem;border-radius:8px;max-width:400px;min-width:300px;text-align:left;position:relative;">
    <h4>Modifier l'utilisateur</h4>
    <form id="editUtilisateurForm">
      <input type="hidden" id="editUtilisateurId">
      <div style="margin-bottom:1rem;">
        <label>Prénom</label>
        <input type="text" id="editPrenomUtilisateur" class="form-control" required>
      </div>
      <div style="margin-bottom:1rem;">
        <label>Nom</label>
        <input type="text" id="editNomUtilisateur" class="form-control" required>
      </div>
      <div style="margin-bottom:1rem;">
        <label>Email</label>
        <input type="email" id="editEmailUtilisateur" class="form-control" required>
      </div>
      <div style="margin-bottom:1rem;">
        <label>Téléphone</label>
        <input type="text" id="editTelUtilisateur" class="form-control">
      </div>
      <div style="margin-bottom:1rem;">
        <label>Type</label>
        <select id="editTypeUtilisateur" class="form-control">
          <option value="utilisateur">Utilisateur</option>
          <option value="moderateur">Modérateur</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div id="editUtilisateurErrors" style="color:red;margin-bottom:1rem;"></div>
      <div style="text-align:right;">
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <button type="button" class="btn btn-secondary" id="btnCancelEditUtilisateur" style="margin-left:1rem;">Annuler</button>
      </div>
    </form>
  </div>
</div>
