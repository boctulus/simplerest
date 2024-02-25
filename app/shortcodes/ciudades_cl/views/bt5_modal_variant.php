<!--
Flavicon attribution

<a href="https://www.flaticon.com/free-icons/location" title="location icons">Location icons created by Good Ware - Flaticon</a>
-->

<style>
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 50px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 96%;
  height: 380px;
}


/* Modal de ancho completo en dispositivos móviles */
@media only screen and (max-width: 600px) {
  .modal-content {
    max-width: 99%;
  }
}

/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
  .modal-content {
    max-width: 50%;
  }
}

/* Large devices (laptops/desktops, 992px and up) */
@media only screen and (min-width: 992px) {
  .modal-content {
    max-width: 66%;
  }
}

/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 1200px) {
  .modal-content {
    max-width: 50%;
  }
}

@media only screen and (min-width: 1366px) {
  .modal-content {
    max-width: 30%;
  }
}


/* The Close Button */
.btn-close {
  color: #aaaaaa;
  float: right;
  font-size: 12px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>

<div id="addressModal" class="modal">
  <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
    <p>
      <?php include __DIR__ . '/dropdowns.php'; ?>
    </p>
  </div>
</div>

<script>
  const showAddrModal = () => {
    document.getElementById('addressModal').style.display = 'block';
  }

  const closeAddrModal = () => {
    const modal = document.getElementById('addressModal');
    modal.style.display = 'none';
  }

  // Abrir la ventana modal al cargar la página
  window.onload = function () {
    // Cerrar la ventana modal al hacer clic en la "X"
    document.getElementsByClassName('btn-close')[0].onclick = function () {
      document.getElementById('addressModal').style.display = 'none';
    }

    // Cerrar la ventana modal al presionar la tecla "ESC"
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeModal();
      }
    });


    showAddrModal();
  }

</script>