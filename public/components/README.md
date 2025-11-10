# Sistema de Componentes Web

Sistema de carga dinámica de componentes web para SimpleRest.

**Autor:** Pablo Bozzolo (boctulus)
**Versión:** 1.0.0

## Características

- ✅ Carga dinámica de componentes HTML, CSS y JS
- ✅ Componentes reutilizables y modulares
- ✅ Compatible con Bootstrap 5
- ✅ Sin dependencias de ES6 modules (compatible con navegadores antiguos)
- ✅ Sistema de eventos para comunicación entre componentes
- ✅ API pública para manipular componentes

## Componentes Disponibles

### 1. modal-tabs

Modal con pestañas (tabs) usando Bootstrap.

**Características:**
- Soporte para múltiples pestañas
- Botones personalizables
- Prellenado de datos
- Eventos personalizados

**Ejemplo de uso:**
```javascript
await ComponentLoader.loadComponent('#container', 'modal-tabs', {
  title: 'Mi Modal',
  tabs: [
    { id: 'tab1', label: 'Tab 1', content: '<p>Contenido 1</p>' },
    { id: 'tab2', label: 'Tab 2', content: '<p>Contenido 2</p>' }
  ],
  buttons: [
    { text: 'Cerrar', class: 'btn-secondary', dismiss: true },
    { text: 'Guardar', class: 'btn-primary', onClick: (ctx) => { /* ... */ } }
  ]
});
```

### 2. form-modal-tabs

Formulario en modal con pestañas, validación y envío a API.

**Características:**
- Validación de formularios
- Envío automático a API
- Soporte para múltiples pestañas con formularios
- Prellenado de datos desde API
- Eventos de guardado

**Ejemplo de uso:**
```javascript
await ComponentLoader.loadComponent('#container', 'form-modal-tabs', {
  mode: 'create', // 'create', 'edit', 'view'
  title: 'Crear Producto',
  method: 'POST',
  submitUrl: '/api/products',
  tabs: [
    {
      id: 'general',
      label: 'General',
      content: `
        <form class="needs-validation" novalidate>
          <input type="text" name="name" required>
        </form>
      `
    }
  ]
});

// Escuchar evento de guardado
$('#container').on('formSaved', function(e, result) {
  console.log('Guardado:', result);
});
```

### 3. select-plus

Select nativo con botón "+" para crear nuevos elementos vía modal.

**Características:**
- Select nativo de HTML
- Carga de opciones desde API
- Botón "+" para crear nuevos elementos
- Modal configurable para creación
- Prellenado de valores iniciales
- API pública para manipular opciones

**Ejemplo de uso:**
```javascript
await ComponentLoader.loadComponent('#container', 'select-plus', {
  label: 'Categoría',
  name: 'category_id',
  apiUrl: '/api/categories',
  placeholder: 'Selecciona...',
  required: true,
  fetchOnInit: true,
  textField: 'name',
  valueField: 'id',
  modalOptions: {
    title: 'Nueva Categoría',
    submitUrl: '/api/categories',
    tabs: [
      {
        id: 'general',
        label: 'Datos',
        content: '<form class="needs-validation" novalidate>...</form>'
      }
    ]
  },
  onChange: (data) => console.log('Seleccionado:', data),
  onCreateSuccess: (data) => console.log('Creado:', data)
});
```

**API Pública:**
```javascript
const $root = $('#container');

// Obtener valor
const value = window.Components['select-plus'].getValue($root);

// Setear valor
window.Components['select-plus'].setValue($root, '123');

// Agregar opción
window.Components['select-plus'].addOption($root, '456', 'Nueva opción', true);

// Eliminar opción
window.Components['select-plus'].removeOption($root, '456');

// Limpiar opciones
window.Components['select-plus'].clearOptions($root);

// Cargar opciones desde API
await window.Components['select-plus'].loadOptionsFromAPI($root, '/api/items');
```

### 4. select2-plus

Select2 con búsqueda AJAX y botón "+" para crear nuevos elementos.

**Características:**
- Select2 con búsqueda en tiempo real
- AJAX para cargar opciones dinámicamente
- Botón "+" para crear nuevos elementos
- Soporte para selección múltiple
- Prellenado de valores iniciales
- Temas Bootstrap 5

**Ejemplo de uso:**
```javascript
await ComponentLoader.loadComponent('#container', 'select2-plus', {
  label: 'Usuario',
  name: 'user_id',
  apiUrl: '/api/users',
  placeholder: 'Buscar usuario...',
  minimumInputLength: 2,
  textField: 'name',
  valueField: 'id',
  multiple: false,
  allowClear: true,
  modalOptions: {
    title: 'Nuevo Usuario',
    submitUrl: '/api/users',
    tabs: [
      {
        id: 'general',
        label: 'Info',
        content: '<form class="needs-validation" novalidate>...</form>'
      }
    ]
  },
  onSelect: (data) => console.log('Seleccionado:', data),
  onCreateSuccess: (data) => console.log('Creado:', data)
});
```

**API Pública:**
```javascript
const $root = $('#container');

// Obtener valor
const value = window.Components['select2-plus'].getValue($root);

// Setear valor
window.Components['select2-plus'].setValue($root, '123');

// Destruir componente
window.Components['select2-plus'].destroy($root);
```

## ComponentLoader API

### loadComponent(targetSelector, componentName, options)

Carga un componente dinámicamente.

**Parámetros:**
- `targetSelector` (string|HTMLElement): Selector CSS o elemento DOM
- `componentName` (string): Nombre del componente a cargar
- `options` (object): Opciones de configuración del componente

**Retorna:** `Promise<void>`

**Ejemplo:**
```javascript
await ComponentLoader.loadComponent('#my-container', 'modal-tabs', {
  title: 'Mi Modal',
  tabs: [...]
});
```

### unloadComponent(componentName)

Descarga un componente (limpia CSS y JS cargados).

**Parámetros:**
- `componentName` (string): Nombre del componente

**Ejemplo:**
```javascript
ComponentLoader.unloadComponent('modal-tabs');
```

## Helpers de Formularios

### getFormData(formElem, use_id, prefix)

Obtiene los datos de un formulario como objeto.

**Parámetros:**
- `formElem` (jQuery): Elemento del formulario
- `use_id` (boolean): Si usar id en lugar de name
- `prefix` (string|null): Prefijo a remover de los nombres

**Retorna:** `object`

**Ejemplo:**
```javascript
const $form = $('form.my-form');
const data = getFormData($form, false, null);
console.log(data); // { name: 'Juan', email: 'juan@example.com' }
```

### fillForm(data_obj, prefix)

Rellena un formulario con datos.

**Parámetros:**
- `data_obj` (object): Objeto con los datos
- `prefix` (string|null): Prefijo para los ids

**Ejemplo:**
```javascript
fillForm({ name: 'Juan', email: 'juan@example.com' }, null);
```

## Estructura de un Componente

Cada componente debe tener 3 archivos en su carpeta:

```
public/components/
  └── mi-componente/
      ├── mi-componente.html    # Template HTML
      ├── mi-componente.css     # Estilos CSS
      └── mi-componente.js      # Lógica JavaScript
```

### Ejemplo de componente básico:

**mi-componente.html:**
```html
<div class="mi-componente">
  <h3 class="titulo"></h3>
  <p class="contenido"></p>
</div>
```

**mi-componente.css:**
```css
.mi-componente {
  padding: 20px;
  border: 1px solid #ddd;
}
```

**mi-componente.js:**
```javascript
(function(window) {
  'use strict';

  function init($root, options) {
    options = options || {};
    const titulo = options.titulo || 'Título por defecto';
    const contenido = options.contenido || 'Contenido por defecto';

    $root.find('.titulo').text(titulo);
    $root.find('.contenido').text(contenido);
  }

  // Registrar el componente
  if (!window.Components) {
    window.Components = {};
  }

  window.Components['mi-componente'] = {
    init: init
  };

})(window);
```

**Uso:**
```javascript
await ComponentLoader.loadComponent('#container', 'mi-componente', {
  titulo: 'Hola Mundo',
  contenido: 'Este es mi componente!'
});
```

## Dependencias

### Requeridas:
- jQuery 3.7+
- Bootstrap 5.3+

### Opcionales:
- Select2 4.1+ (para select2-plus)
- Bootstrap Icons (para iconos en botones)

## Instalación

1. Incluir las dependencias en tu HTML:

```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons (opcional) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Select2 (opcional, para select2-plus) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Component Loader y Helpers -->
<script src="/js/componentLoader.js"></script>
<script src="/js/forms.js"></script>
```

2. Usar los componentes:

```javascript
$(document).ready(async function() {
  await ComponentLoader.loadComponent('#my-container', 'modal-tabs', {
    title: 'Mi Modal',
    tabs: [...]
  });
});
```

## Ejemplos

Ver ejemplos interactivos en: `http://localhost/simplerest/components/examples`

O visita la página principal del sistema: `http://localhost/simplerest/components`

## Notas

- Los componentes usan un patrón de namespace global para compatibilidad con navegadores antiguos
- Cada componente se registra en `window.Components['nombre-componente']`
- Los estilos CSS se inyectan una sola vez en el `<head>`
- Los scripts JS se cargan una sola vez y se cachean

## Licencia

Desarrollado por Pablo Bozzolo (boctulus) para SimpleRest Framework.
