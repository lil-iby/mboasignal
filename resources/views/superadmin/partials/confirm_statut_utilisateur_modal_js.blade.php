<script>
function showStatutUtilisateurConfirmModal({id, action, confirmMsg, onConfirm}) {
  const modal = document.getElementById('confirmStatutUtilisateurModal');
  document.getElementById('confirmStatutUtilisateurMsg').textContent = confirmMsg;
  modal.style.display = 'flex';

  const btnConfirm = document.getElementById('btnConfirmStatutUtilisateur');
  const btnCancel = document.getElementById('btnCancelStatutUtilisateur');
  btnConfirm.onclick = null;
  btnCancel.onclick = null;

  btnConfirm.onclick = async function() {
    modal.style.display = 'none';
    if (onConfirm) await onConfirm();
  };
  btnCancel.onclick = function() {
    modal.style.display = 'none';
  };
}
window.addEventListener('click', function(e) {
  const modal = document.getElementById('confirmStatutUtilisateurModal');
  if (modal && e.target === modal) {
    modal.style.display = 'none';
  }
});
</script>
