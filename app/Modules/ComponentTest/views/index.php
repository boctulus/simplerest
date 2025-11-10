<div class="row justify-content-center mt-5">
  <div class="col-lg-8">
    <div class="text-center mb-5">
      <h1 class="display-4">Sistema de Componentes Web</h1>
      <p class="lead text-muted">
        SimpleRest Framework - Sistema de carga dinámica de componentes
      </p>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h2 class="h4 card-title mb-4">
          <i class="bi bi-puzzle text-primary"></i> Componentes Disponibles
        </h2>

        <div class="list-group mb-4">
          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">modal-tabs</h5>
              <span class="badge bg-primary rounded-pill">Listo</span>
            </div>
            <p class="mb-1">Modal con pestañas usando Bootstrap 5</p>
            <small class="text-muted">Soporte para múltiples tabs, botones personalizados y eventos</small>
          </div>

          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">form-modal-tabs</h5>
              <span class="badge bg-primary rounded-pill">Listo</span>
            </div>
            <p class="mb-1">Formulario en modal con validación y envío a API</p>
            <small class="text-muted">Validación automática, prellenado de datos, eventos de guardado</small>
          </div>

          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">select-plus</h5>
              <span class="badge bg-primary rounded-pill">Listo</span>
            </div>
            <p class="mb-1">Select nativo con botón "+" para crear nuevos elementos</p>
            <small class="text-muted">Carga desde API, modal de creación, API pública completa</small>
          </div>

          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1">select2-plus</h5>
              <span class="badge bg-primary rounded-pill">Listo</span>
            </div>
            <p class="mb-1">Select2 con búsqueda AJAX y botón "+" para crear elementos</p>
            <small class="text-muted">Búsqueda en tiempo real, soporte multi-select, temas Bootstrap 5</small>
          </div>
        </div>

        <div class="d-grid gap-2">
          <a href="/components/examples" class="btn btn-primary btn-lg">
            <i class="bi bi-play-circle"></i> Ver Ejemplos Interactivos
          </a>
          <a href="/public/components/README.md" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-book"></i> Documentación Completa
          </a>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <div class="card border-0 bg-light">
        <div class="card-body">
          <h3 class="h6 card-title">
            <i class="bi bi-info-circle text-info"></i> Características
          </h3>
          <ul class="mb-0">
            <li>Carga dinámica de componentes (HTML, CSS, JS)</li>
            <li>Compatible con Bootstrap 5 y jQuery</li>
            <li>Sin dependencias de ES6 modules</li>
            <li>Sistema de eventos para comunicación entre componentes</li>
            <li>API pública para manipular componentes</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="text-center mt-4 text-muted small">
      <p class="mb-0">
        <strong>Autor:</strong> Pablo Bozzolo (boctulus) |
        <strong>Versión:</strong> 1.0.0
      </p>
    </div>
  </div>
</div>

<?php
// Cargar Bootstrap Icons
css_file('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css', true);
?>
