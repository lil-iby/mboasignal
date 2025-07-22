<script>
function showStatutConfirmModal({id, action, confirmMsg, onConfirm}) {
  const modal = document.getElementById('confirmStatutModal');
  document.getElementById('confirmStatutMsg').textContent = confirmMsg;
  modal.style.display = 'flex';

  // Nettoyage listeners précédents
  const btnConfirm = document.getElementById('btnConfirmStatut');
  const btnCancel = document.getElementById('btnCancelStatut');
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
// Fermer la modale si clic hors contenu
window.addEventListener('click', function(e) {
  const modal = document.getElementById('confirmStatutModal');
  if (modal && e.target === modal) {
    modal.style.display = 'none';
  }
});
</script>
