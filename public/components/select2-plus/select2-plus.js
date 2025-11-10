/**
 * Select2-Plus Component
 *
 * Componente que combina un Select2 con un botón "+" para crear nuevos elementos
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */

(function(window) {
  'use strict';

  /**
   * Inicializa el componente select2-plus
   */
  async function init($root, options) {
    options = options || {};

    const label = options.label || 'Seleccionar';
    const name = options.name || 'field_id';
    const apiUrl = options.apiUrl || '';
    const placeholder = options.placeholder || 'Buscar o seleccionar...';
    const use_typesense = options.use_typesense || false;
    const required = options.required || false;
    const multiple = options.multiple || false;
    const modalComponent = options.modalComponent || 'form-modal-tabs';
    const modalOptions = options.modalOptions || {};
    const onSelect = options.onSelect || null;
    const onCreateSuccess = options.onCreateSuccess || null;
    const minimumInputLength = options.minimumInputLength !== undefined ? options.minimumInputLength : 2;
    const searchFields = options.searchFields || null;
    const textField = options.textField || 'name';
    const valueField = options.valueField || 'id';
    const initialValue = options.initialValue || null;
    const initialData = options.initialData || null;
    const allowClear = options.allowClear !== undefined ? options.allowClear : true;

    if (!apiUrl) {
      console.error('select2-plus: apiUrl es requerido');
      return;
    }

    const $container = $root.find('.select2-plus-container');
    const $select = $container.find('.select2-plus-select');
    const $btnPlus = $container.find('.btn-plus');
    const $modalContainer = $container.find('.select2-plus-modal-container');

    // Set select attributes
    $select.attr('name', name);
    if (required) {
      $select.attr('required', 'required');
    }
    if (multiple) {
      $select.attr('multiple', 'multiple');
    }

    // Determine which API to use
    const searchApiUrl = use_typesense
      ? apiUrl.replace('/api/firestore/', '/api/typesense/')
      : apiUrl.replace('/api/typesense/', '/api/firestore/');

    console.log('🔧 select2-plus: Usando API ' + (use_typesense ? 'Typesense' : 'Firebase'));
    console.log('   Endpoint de búsqueda: ' + searchApiUrl);
    console.log('   Endpoint de creación: ' + apiUrl);

    // Initialize Select2 with AJAX
    $select.select2({
      theme: 'bootstrap-5',
      placeholder: placeholder,
      allowClear: allowClear,
      minimumInputLength: minimumInputLength,
      ajax: {
        url: searchApiUrl,
        dataType: 'json',
        delay: 300,
        data: function(params) {
          const queryParams = {
            q: params.term || '*',
            limit: 50
          };

          // Add search fields if specified
          if (searchFields) {
            queryParams.searchFields = searchFields;
          }

          return queryParams;
        },
        processResults: function(response) {
          console.log('🔍 select2-plus: Resultados de búsqueda:', response);

          // Handle both Firebase and Typesense response formats
          const docs = (response.data && response.data.docs) || response.docs || response.data || [];

          const results = docs.map(function(item) {
            return {
              id: item[valueField] || item.id,
              text: item[textField] || item.name || item.description || item.id
            };
          });

          return { results: results };
        },
        error: function(xhr, status, error) {
          // Ignore abort errors
          if (error !== 'abort') {
            console.error('❌ select2-plus: Error en búsqueda AJAX:', error, status);
          }
          return { results: [] };
        }
      }
    });

    // Load initial values if provided
    if (initialValue || initialData) {
      setTimeout(async function() {
        await loadInitialOptions($select, initialValue, initialData, apiUrl, textField, valueField);
      }, 100);
    }

    // Handle select change event
    $select.on('change', function() {
      const selectedValues = $select.val();
      const selectedData = $select.select2('data');

      console.log('📝 select2-plus: Valor seleccionado:', selectedValues);

      if (typeof onSelect === 'function') {
        if (multiple) {
          onSelect({ ids: selectedValues, data: selectedData });
        } else {
          onSelect({
            id: selectedValues,
            text: selectedData[0] ? selectedData[0].text : ''
          });
        }
      }
    });

    // Handle "+" button click
    $btnPlus.on('click', async function() {
      console.log('➕ select2-plus: Abriendo modal para crear nuevo elemento');
      console.log('   - apiUrl:', apiUrl);
      console.log('   - textField:', textField);
      console.log('   - valueField:', valueField);

      // Prepare modal options
      const finalModalOptions = {
        mode: 'create',
        title: modalOptions.title || ('Nuevo ' + label),
        method: 'POST',
        submitUrl: apiUrl,
        tabs: modalOptions.tabs || [
          {
            id: 'general',
            label: 'General',
            content: modalOptions.content || '<p>No se ha proporcionado contenido para el formulario</p>'
          }
        ]
      };

      // Merge any additional modal options
      for (var key in modalOptions) {
        if (modalOptions.hasOwnProperty(key) && key !== 'tabs' && key !== 'title') {
          finalModalOptions[key] = modalOptions[key];
        }
      }

      console.log('📝 select2-plus: Opciones del modal:', finalModalOptions);

      // Load form-modal-tabs component
      await ComponentLoader.loadComponent($modalContainer[0], modalComponent, finalModalOptions);

      // Listen for formSaved event
      $modalContainer.one('formSaved', async function(event, result) {
        console.log('✅ select2-plus: Formulario guardado:', result);
        console.log('   - result.mode:', result.mode);
        console.log('   - result.data:', result.data);
        console.log('   - result.response:', result.response);

        try {
          // Extract the created item from the server response
          const serverResponse = result.response;
          console.log('📦 select2-plus: Respuesta del servidor:', serverResponse);

          // Handle both Firebase and standard response formats
          const createdItem = (serverResponse && serverResponse.data) || serverResponse || result.data;
          console.log('🔍 select2-plus: Datos del item creado:', createdItem);
          console.log('   - valueField:', valueField, '- textField:', textField);

          const newId = createdItem[valueField] || createdItem.id;
          const newText = createdItem[textField] || createdItem.name || createdItem.description || newId;

          console.log('🆕 select2-plus: Nuevo elemento creado:', { id: newId, text: newText });

          // Add new option to Select2
          const newOption = new Option(newText, newId, true, true);
          $select.append(newOption);
          $select.trigger('change');

          console.log('✅ select2-plus: Elemento agregado al Select2');

          // Call success callback
          if (typeof onCreateSuccess === 'function') {
            onCreateSuccess({
              id: newId,
              label: newText,
              data: createdItem
            });
          }

        } catch (error) {
          console.error('❌ select2-plus: Error al procesar elemento creado:', error);
          alert('Error al agregar el nuevo elemento. Por favor, recarga la página.');
        }
      });
    });

    // Cleanup on destroy
    $root.data('select2-plus-destroy', function() {
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
      }
      $select.off();
      $btnPlus.off();
      $modalContainer.empty();
    });

    console.log('✅ select2-plus: Componente inicializado');
  }

  /**
   * Load initial options for the Select2
   */
  async function loadInitialOptions($select, initialValue, initialData, apiUrl, textField, valueField) {
    console.log('🔄 select2-plus: Cargando valores iniciales...');

    // If we have the data objects directly, use them
    if (initialData) {
      const dataArray = Array.isArray(initialData) ? initialData : [initialData];

      dataArray.forEach(function(item) {
        const id = item[valueField] || item.id;
        const text = item[textField] || item.name || item.description || id;
        const option = new Option(text, id, true, true);
        $select.append(option);
      });

      $select.trigger('change');
      console.log('✅ select2-plus: ' + dataArray.length + ' valor(es) inicial(es) cargado(s)');
      return;
    }

    // Otherwise, fetch by IDs
    if (!initialValue) {
      return;
    }

    const ids = Array.isArray(initialValue) ? initialValue : [initialValue];

    const dataPromises = ids.map(async function(id) {
      try {
        const response = await fetch(apiUrl + '/' + id);
        if (response.ok) {
          const json = await response.json();
          return json.data || json;
        }
        console.warn('⚠️ select2-plus: No se pudo cargar el elemento ' + id);
        return null;
      } catch (error) {
        console.error('❌ select2-plus: Error al cargar ' + id + ':', error);
        return null;
      }
    });

    const items = await Promise.all(dataPromises);
    const validItems = items.filter(function(item) { return item !== null; });

    console.log('✅ select2-plus: Elementos cargados:', validItems);

    validItems.forEach(function(item) {
      const id = item[valueField] || item.id;
      const text = item[textField] || item.name || item.description || id;
      const option = new Option(text, id, true, true);
      $select.append(option);
    });

    $select.trigger('change');
    console.log('✅ select2-plus: ' + validItems.length + ' valor(es) inicial(es) cargado(s)');
  }

  /**
   * Public API
   */
  function getValue($root) {
    const $select = $root.find('.select2-plus-select');
    return $select.val();
  }

  function setValue($root, value) {
    const $select = $root.find('.select2-plus-select');
    $select.val(value).trigger('change');
  }

  function destroy($root) {
    const destroyFn = $root.data('select2-plus-destroy');
    if (destroyFn) {
      destroyFn();
    }
  }

  // Registrar el componente
  if (!window.Components) {
    window.Components = {};
  }

  window.Components['select2-plus'] = {
    init: init,
    getValue: getValue,
    setValue: setValue,
    destroy: destroy
  };

  window.select2_plus = {
    init: init,
    getValue: getValue,
    setValue: setValue,
    destroy: destroy
  };

})(window);
