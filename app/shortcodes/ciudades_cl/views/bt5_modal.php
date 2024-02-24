<?php
  css('https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css');
?>


<style>
  /* Agrega tu clase de CSS personalizada para el ancho y bordes redondeados */
  .custom-modal {
    border-radius: 10px;
  }

  .modal-dialog {
    height: 80%; /* = 90% of the .modal-backdrop block = %90 of the screen */
  }

  .modal-content {
    height: 50%; /* = 100% of the .modal-dialog block */
  }
</style>

<!-- HTML del modal -->
<div class="modal fade custom-modal" tabindex="-1" id="myModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      
      <div class="modal-body">
        <?php include __DIR__ . '/dropdowns.php'; ?>
      </div>
    </div>
  </div>
</div>


<script>
  const showMyModal = () => {
    const myModal = new bootstrap.Modal(document.getElementById('myModal'));
    myModal.show();
  }

  // Abrir la ventana modal al cargar la página
  window.onload = function () {
    showMyModal();
  }
</script>
