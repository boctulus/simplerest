/**
 * Form Modal Tabs Component
 *
 * Componente de formulario en modal con pestañas
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */

(function(window) {
  'use strict';

  /**
   * Inicializa el componente form-modal-tabs
   *
   * @param {jQuery} $root - Elemento raíz
   * @param {object} options - Opciones de configuración
   */
  async function init($root, options) {
    options = options || {};

    const mode = options.mode || 'view';
    const title = options.title || 'Formulario';
    const tabs = options.tabs || [];
    const method = options.method || 'POST';
    const submitUrl = options.submitUrl || '';
    const onInit = options.onInit || null;

    const isReadOnly = mode === 'view';
    const needsSubmit = mode === 'edit' || mode === 'create';

    const $container = $root.find('.forms-modal-tabs-container');

    const modalTabsOptions = {
      title: title,
      tabs: tabs.map(function(tab) {
        return {
          id: tab.id,
          label: tab.label,
          content: tab.content
        };
      }),
      buttons: !needsSubmit
        ? [{ text: 'Cerrar', class: 'btn-secondary', dismiss: true }]
        : [
          {
            id: 'btnSave',
            text: 'Guardar',
            class: 'btn-primary',
            onClick: async function(context) {
              const modal = context.modal;
              const $rootElem = context.$root;
              const $btn = context.$btn;

              const originalHtml = $btn.html();
              $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
              $btn.prop('disabled', true);

              let isValid = true;
              const $modalElem = $container.find('.modal-tabs');
              const $forms = $modalElem.find('form.needs-validation');

              console.log('🔍 Formularios encontrados: ' + $forms.length);

              if ($forms.length === 0) {
                console.warn('❌ Formularios no encontrados. Selector usado: form.needs-validation');
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                return;
              }

              // Validar cada formulario
              $forms.each(function(_, form) {
                $(form).find(':input').each(function(__, field) {
                  const $field = $(field);
                  const required = $field.prop('required');
                  const rawValue = $field.val();

                  let value;
                  if (Array.isArray(rawValue)) {
                    value = rawValue;
                  } else if (typeof rawValue === 'string') {
                    value = rawValue.trim();
                  } else {
                    value = rawValue;
                  }

                  let errorMessage = '';

                  // Validation for required fields
                  if (required) {
                    if (Array.isArray(value)) {
                      if (value.length === 0) {
                        errorMessage = $field.data('required-message') || 'Este campo es obligatorio.';
                      }
                    } else if (value === '' || value === null || value === undefined) {
                      errorMessage = $field.data('required-message') || 'Este campo es obligatorio.';
                    }
                  }

                  // Email validation
                  if (!errorMessage && $field.attr('type') === 'email' && value !== '' && value !== null) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                      errorMessage = $field.data('invalid-message') || 'Formato de correo inválido.';
                    }
                  }

                  if (errorMessage) {
                    $field.addClass('is-invalid');
                    $field.siblings('.invalid-feedback').text(errorMessage);
                    isValid = false;
                  } else {
                    $field.removeClass('is-invalid');
                  }
                });
              });

              if (!isValid) {
                alert('Por favor, corrige los errores en el formulario.');
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                return;
              }

              // Recolectar datos de todos los formularios
              const allData = {};
              $forms.each(function(index, form) {
                const formData = getFormData($(form), false);
                Object.assign(allData, formData);
              });

              console.log('📦 Datos finales a enviar:', allData);

              // Enviar datos
              try {
                const response = await fetch(submitUrl, {
                  method: method,
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(allData)
                });

                let responseData;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                  responseData = await response.json();
                }

                if (response.status === 401) {
                  const errorMsg = responseData && responseData.code === 'SESSION_EXPIRED'
                    ? responseData.error
                    : 'Sesión expirada. Por favor, recarga la página para iniciar sesión.';

                  alert(errorMsg);
                  if (confirm('¿Deseas recargar la página para iniciar sesión?')) {
                    window.location.reload();
                  }
                  return;
                }

                if (response.ok) {
                  $root.trigger('formSaved', { mode: mode, data: allData, response: responseData });
                  modal.hide();
                } else {
                  const errorMsg = (responseData && responseData.error) || response.statusText || 'Error desconocido';
                  console.error('Error al guardar:', errorMsg);
                  alert('Hubo un error al guardar los datos: ' + errorMsg);
                }
              } catch (error) {
                console.error('Error en la petición:', error);
                alert('Error de conexión al guardar.');
              } finally {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
              }
            }
          },
          {
            id: 'btnCancel',
            text: 'Cancelar',
            class: 'btn-secondary',
            dismiss: true
          }
        ]
    };

    // Cargar el componente modal-tabs
    await ComponentLoader.loadComponent($container[0], 'modal-tabs', modalTabsOptions);

    const $modal = $container.find('.modal-tabs');
    const $overlay = $modal.find('.modal-loading-overlay');
    const hasDataToFetch = tabs.some(function(tab) { return tab.dataUrl; });

    // Mostrar el spinner si hay datos que cargar
    if (hasDataToFetch) {
      $overlay.removeClass('d-none');
    }

    $modal.modal('show');

    // Cargar datos para cada pestaña con dataUrl
    for (let i = 0; i < tabs.length; i++) {
      const tab = tabs[i];
      if (tab.dataUrl) {
        try {
          console.log('🔄 Cargando datos para tab "' + tab.id + '" desde: ' + tab.dataUrl);
          const res = await fetch(tab.dataUrl);
          if (!res.ok) throw new Error('Error al cargar datos');
          const json = await res.json();
          const data = json.data || json;
          console.log('✅ Datos recibidos para tab "' + tab.id + '":', data);
          fillForm(data, '');
        } catch (err) {
          console.error('❌ Error al cargar datos para tab ' + tab.id + ':', err);
        }
      }
    }

    // Ocultar el spinner después de cargar los datos
    if (hasDataToFetch) {
      $overlay.addClass('d-none');
    }

    // Si es readonly, deshabilitar inputs
    if (isReadOnly) {
      $modal.find(':input').each(function(_, input) {
        $(input).prop('readonly', true).addClass('bg-light');
      });
      $modal.find('select, input[type="checkbox"], input[type="radio"]').prop('disabled', true);
    }

    // Cleanup al cerrar el modal
    $modal.on('hidden.bs.modal', function() {
      $modal.off();
      $container.empty();
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css('overflow', '');
    });

    // Callback de inicialización
    if (typeof onInit === 'function') {
      onInit({ $modal: $modal, $container: $container });
    }
  }

  // Registrar el componente
  if (!window.Components) {
    window.Components = {};
  }

  window.Components['form-modal-tabs'] = {
    init: init
  };

  window.form_modal_tabs = {
    init: init
  };

})(window);
