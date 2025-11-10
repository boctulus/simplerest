<div class="d-flex justify-content-between align-items-center mb-4">
  <h1>Sistema de Componentes - Ejemplos</h1>
  <a href="/components" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Volver
  </a>
</div>

<div class="alert alert-info">
  <i class="bi bi-info-circle"></i>
  <strong>Nota:</strong> Estos ejemplos usan APIs públicas de prueba (jsonplaceholder.typicode.com) para demostración.
  Los datos creados no se guardarán realmente.
</div>

<!-- Ejemplo 1: Modal Tabs Simple -->
<section class="mb-5">
  <div class="card">
    <div class="card-header">
      <h2 class="h5 mb-0">1. Modal con Tabs</h2>
    </div>
    <div class="card-body">
      <p class="text-muted">Modal con múltiples pestañas, botones personalizados y callbacks.</p>
      <button id="btnModalTabs" class="btn btn-primary">
        <i class="bi bi-window"></i> Abrir Modal con Tabs
      </button>
      <div id="modal-tabs-container"></div>
    </div>
  </div>
</section>

<!-- Ejemplo 2: Select Plus -->
<section class="mb-5">
  <div class="card">
    <div class="card-header">
      <h2 class="h5 mb-0">2. Select Plus (Select + Botón Crear)</h2>
    </div>
    <div class="card-body">
      <p class="text-muted">Select nativo con botón "+" para crear nuevos elementos vía modal.</p>
      <div id="select-plus-container"></div>
    </div>
  </div>
</section>

<!-- Ejemplo 3: Select2 Plus -->
<section class="mb-5">
  <div class="card">
    <div class="card-header">
      <h2 class="h5 mb-0">3. Select2 Plus (Select2 con AJAX + Botón Crear)</h2>
    </div>
    <div class="card-body">
      <p class="text-muted">Select2 con búsqueda AJAX en tiempo real y botón "+" para crear elementos.</p>
      <div id="select2-plus-container"></div>
    </div>
  </div>
</section>

<!-- Ejemplo 4: Form Modal Tabs -->
<section class="mb-5">
  <div class="card">
    <div class="card-header">
      <h2 class="h5 mb-0">4. Formulario en Modal con Tabs</h2>
    </div>
    <div class="card-body">
      <p class="text-muted">Formulario completo en modal con validación y envío a API.</p>
      <button id="btnFormModal" class="btn btn-success">
        <i class="bi bi-file-earmark-text"></i> Abrir Formulario Modal
      </button>
      <div id="form-modal-container"></div>
    </div>
  </div>
</section>

<script>
  $(document).ready(function() {

    // Ejemplo 1: Modal Tabs
    $('#btnModalTabs').on('click', async function() {
      await ComponentLoader.loadComponent('#modal-tabs-container', 'modal-tabs', {
        title: 'Mi Modal con Tabs',
        tabs: [
          {
            id: 'info',
            label: 'Información',
            content: '<p>Este es el contenido de la pestaña de información.</p>'
          },
          {
            id: 'settings',
            label: 'Configuración',
            content: '<p>Este es el contenido de la pestaña de configuración.</p>'
          },
          {
            id: 'advanced',
            label: 'Avanzado',
            content: '<p>Este es el contenido de la pestaña avanzada.</p>'
          }
        ],
        buttons: [
          {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
          },
          {
            text: 'Guardar',
            class: 'btn-primary',
            onClick: function(context) {
              alert('¡Guardado!');
              context.modal.hide();
            }
          }
        ]
      });
    });

    // Ejemplo 2: Select Plus
    (async function() {
      await ComponentLoader.loadComponent('#select-plus-container', 'select-plus', {
        label: 'Categoría',
        name: 'category_id',
        apiUrl: 'https://jsonplaceholder.typicode.com/users', // API de ejemplo
        placeholder: 'Selecciona una categoría...',
        required: true,
        textField: 'name',
        valueField: 'id',
        fetchOnInit: true, // Cargar opciones al iniciar
        onChange: function(data) {
          console.log('Categoría seleccionada:', data);
        },
        modalOptions: {
          title: 'Nueva Categoría',
          submitUrl: 'https://jsonplaceholder.typicode.com/users', // API de ejemplo
          tabs: [
            {
              id: 'general',
              label: 'Datos Generales',
              content: `
                <form class="needs-validation" novalidate>
                  <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                    <div class="invalid-feedback">El nombre es requerido</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                    <div class="invalid-feedback">El email es requerido</div>
                  </div>
                </form>
              `
            }
          ]
        },
        onCreateSuccess: function(data) {
          console.log('Nueva categoría creada:', data);
          alert('Categoría "' + data.label + '" creada exitosamente!');
        }
      });
    })();

    // Ejemplo 3: Select2 Plus
    (async function() {
      await ComponentLoader.loadComponent('#select2-plus-container', 'select2-plus', {
        label: 'Usuario',
        name: 'user_id',
        apiUrl: 'https://jsonplaceholder.typicode.com/users', // API de ejemplo
        placeholder: 'Buscar usuario...',
        textField: 'name',
        valueField: 'id',
        minimumInputLength: 0,
        onSelect: function(data) {
          console.log('Usuario seleccionado:', data);
        },
        modalOptions: {
          title: 'Nuevo Usuario',
          submitUrl: 'https://jsonplaceholder.typicode.com/users',
          tabs: [
            {
              id: 'general',
              label: 'Información',
              content: `
                <form class="needs-validation" novalidate>
                  <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                    <div class="invalid-feedback">El nombre es requerido</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                    <div class="invalid-feedback">El email es requerido</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="phone" class="form-control">
                  </div>
                </form>
              `
            }
          ]
        },
        onCreateSuccess: function(data) {
          console.log('Nuevo usuario creado:', data);
          alert('Usuario "' + data.label + '" creado exitosamente!');
        }
      });
    })();

    // Ejemplo 4: Form Modal Tabs
    $('#btnFormModal').on('click', async function() {
      await ComponentLoader.loadComponent('#form-modal-container', 'form-modal-tabs', {
        mode: 'create', // 'create', 'edit', o 'view'
        title: 'Crear Nuevo Producto',
        method: 'POST',
        submitUrl: 'https://jsonplaceholder.typicode.com/posts',
        tabs: [
          {
            id: 'general',
            label: 'Información General',
            content: `
              <form class="needs-validation" novalidate>
                <div class="mb-3">
                  <label class="form-label">Título</label>
                  <input type="text" name="title" class="form-control" required>
                  <div class="invalid-feedback">El título es requerido</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <textarea name="body" class="form-control" rows="3" required></textarea>
                  <div class="invalid-feedback">La descripción es requerida</div>
                </div>
              </form>
            `
          },
          {
            id: 'additional',
            label: 'Información Adicional',
            content: `
              <form class="needs-validation" novalidate>
                <div class="mb-3">
                  <label class="form-label">User ID</label>
                  <input type="number" name="userId" class="form-control" value="1" required>
                  <div class="invalid-feedback">El User ID es requerido</div>
                </div>
              </form>
            `
          }
        ],
        onInit: function(context) {
          console.log('Formulario modal inicializado:', context);
        }
      });

      // Escuchar el evento de guardado
      $('#form-modal-container').one('formSaved', function(e, result) {
        console.log('Formulario guardado:', result);
        alert('¡Producto creado exitosamente!\n\nDatos: ' + JSON.stringify(result.data, null, 2));
      });
    });

  });
</script>
