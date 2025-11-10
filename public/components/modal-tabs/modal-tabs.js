/**
 * Modal Tabs Component
 *
 * Componente de modal con pestañas (tabs) usando Bootstrap
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */

(function(window) {
  'use strict';

  /**
   * Inicializa el componente modal-tabs
   *
   * @param {jQuery} $root - Elemento raíz donde se cargó el componente
   * @param {object} options - Opciones de configuración
   * @param {string} options.title - Título del modal
   * @param {Array} options.tabs - Array de tabs a mostrar
   * @param {object} options.data - Datos para prellenar el formulario
   * @param {Array} options.buttons - Botones personalizados para el footer
   */
  function init($root, options) {
    options = options || {};

    const title = options.title || 'Modal';
    const tabs = options.tabs || [];
    const data = options.data || {};
    const buttons = options.buttons;

    $root.find('.modal-title').text(title);

    const $tabsContainer = $root.find('.tabs-container');
    $tabsContainer.empty();

    let $tabContent = $('<div class="tab-content p-4 h-100"></div>');

    if (tabs.length === 1) {
      // Si solo hay una tab, no mostrar las pestañas
      $tabContent.html(`
        <div class="h-100">
          ${tabs[0].content}
        </div>
      `);
    } else {
      // Crear las pestañas
      const $navTabs = $('<ul class="nav nav-tabs" data-bs-toggle="tabs" role="tablist"></ul>');

      tabs.forEach(function(tab, index) {
        const isActive = index === 0 ? 'active' : '';
        const tabId = `tab-${tab.id || index}`;

        $navTabs.append(`
          <li class="nav-item" role="presentation">
            <a href="#${tabId}" class="nav-link ${isActive}" data-bs-toggle="tab" role="tab">${tab.label}</a>
          </li>
        `);

        $tabContent.append(`
          <div class="tab-pane ${isActive} show h-100" id="${tabId}" role="tabpanel">
            ${tab.content}
          </div>
        `);
      });

      $tabsContainer.append($navTabs);
    }

    $tabsContainer.append($tabContent);

    const modalElement = $root.find('.modal').get(0);
    const modalInstance = new bootstrap.Modal(modalElement);

    // Configurar botones personalizados si se proporcionaron
    if (buttons) {
      const $footer = $root.find('.modal-footer');
      $footer.empty();

      buttons.forEach(function(btn) {
        const $btn = $('<button>')
          .attr('type', btn.type || 'button')
          .addClass(`btn ${btn.class}`)
          .text(btn.text);

        if (!btn.onClick && btn.dismiss) {
          $btn.attr('data-bs-dismiss', 'modal');
        }

        if (btn.id) {
          $btn.attr('id', btn.id);
        }

        if (btn.onClick) {
          $btn.on('click', function(e) {
            btn.onClick({
              modal: modalInstance,
              $root: $root,
              event: e,
              $btn: $btn
            });
          });
        }

        $footer.append($btn);
      });
    }

    // Prellenar datos si se proporcionaron
    Object.keys(data).forEach(function(key) {
      const $input = $root.find(`[name="${key}"]`);
      if ($input.length) {
        $input.val(data[key]);
      }
    });

    // Mostrar el modal
    modalInstance.show();
  }

  // Registrar el componente en el namespace global
  if (!window.Components) {
    window.Components = {};
  }

  window.Components['modal-tabs'] = {
    init: init
  };

  // También crear un namespace específico para el componente
  window.modal_tabs = {
    init: init
  };

})(window);
