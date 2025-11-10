/**
 * Select-Plus Component
 *
 * Componente que combina un SELECT nativo con un botón "+" para crear nuevos elementos
 *
 * @author Pablo Bozzolo (boctulus)
 * @date 2025-11-07
 */

(function(window) {
  'use strict';

  /**
   * Inicializa el componente select-plus
   */
  async function init($root, options) {
    options = options || {};

    const label = options.label || 'Seleccionar';
    const name = options.name || 'field_id';
    const apiUrl = options.apiUrl || '';
    const placeholder = options.placeholder || 'Seleccionar...';
    const required = options.required || false;
    const multiple = options.multiple || false;
    const modalComponent = options.modalComponent || 'form-modal-tabs';
    const modalOptions = options.modalOptions || {};
    const onSelect = options.onSelect || null;
    const onCreateSuccess = options.onCreateSuccess || null;
    const onChange = options.onChange || null;
    const textField = options.textField || 'name';
    const valueField = options.valueField || 'id';
    const initialValue = options.initialValue || null;
    const initialData = options.initialData || null;
    const disabled = options.disabled || false;
    const searchable = options.searchable !== undefined ? options.searchable : true;
    const fetchOnInit = options.fetchOnInit || false;

    if (!apiUrl) {
      console.error('select-plus: apiUrl es requerido');
      return;
    }

    const $container = $root.find('.select-plus-container');
    const $label = $container.find('.select-plus-label');
    const $select = $container.find('.select-plus-select');
    const $btnPlus = $container.find('.btn-plus');
    const $modalContainer = $container.find('.select-plus-modal-container');

    // Set label
    $label.text(label);

    // Set select attributes
    $select.attr('name', name);
    if (required) {
      $select.attr('required', 'required');
    }
    if (multiple) {
      $select.attr('multiple', 'multiple');
    }
    if (disabled) {
      $select.prop('disabled', true);
      $btnPlus.prop('disabled', true);
    }

    // Set placeholder
    $select.find('option:first').text(placeholder);

    console.log('🔧 select-plus: Componente inicializado');
    console.log('   - API: ' + apiUrl);
    console.log('   - Name: ' + name);
    console.log('   - Label: ' + label);

    // Load initial options if fetchOnInit is true
    if (fetchOnInit) {
      await loadOptions($select, apiUrl, textField, valueField);
    }

    // Load initial values if provided
    if (initialValue || initialData) {
      setTimeout(async function() {
        await loadInitialOptions($select, initialValue, initialData, apiUrl, textField, valueField);
      }, 100);
    }

    // Handle select change event
    $select.on('change', function() {
      const selectedValues = $select.val();
      const selectedOptions = $select.find('option:selected');

      console.log('📝 select-plus: Valor seleccionado:', selectedValues);

      // Custom onChange callback
      if (typeof onChange === 'function') {
        onChange({ value: selectedValues, options: selectedOptions });
      }

      // Backward compatibility with onSelect
      if (typeof onSelect === 'function') {
        if (multiple) {
          const ids = [];
          const texts = [];
          selectedOptions.each(function() {
            ids.push($(this).val());
            texts.push($(this).text());
          });
          onSelect({ ids: ids, texts: texts });
        } else {
          onSelect({
            id: selectedValues,
            text: selectedOptions.text()
          });
        }
      }
    });

    // Handle "+" button click
    $btnPlus.on('click', async function() {
      console.log('➕ select-plus: Abriendo modal para crear nuevo elemento');

      // Prepare modal options
      const finalModalOptions = {
        mode: 'create',
        title: modalOptions.title || ('Nuevo ' + label),
        method: 'POST',
        submitUrl: modalOptions.submitUrl || apiUrl,
        tabs: modalOptions.tabs || [
          {
            id: 'general',
            label: 'General',
            content: modalOptions.content || '<p>No se ha proporcionado contenido para el formulario</p>'
          }
        ],
        onInit: modalOptions.onInit || null
      };

      // Load modal component
      await ComponentLoader.loadComponent($modalContainer[0], modalComponent, finalModalOptions);

      // Show modal
      const $modal = $modalContainer.find('.modal');
      $modal.modal('show');

      // Listen for form saved event
      $modalContainer.off('formSaved').on('formSaved', async function(e, result) {
        // IMPORTANTE: Detener la propagación del evento
        e.stopPropagation();

        const response = result.response;

        console.log('✅ select-plus: Formulario guardado');
        console.log('📦 select-plus: Respuesta del servidor:', response);
        console.log('🔍 select-plus: valueField:', valueField);
        console.log('🔍 select-plus: textField:', textField);

        // Extract the new item data
        const newItem = response.data || response;
        console.log('📋 select-plus: newItem:', newItem);

        if (newItem) {
          const newId = newItem[valueField] || newItem.id;
          const newText = newItem[textField] || newItem.name || newItem.description || newId;

          console.log('🆕 select-plus: Nuevo elemento creado:', { id: newId, text: newText, valueField: valueField, textField: textField });

          // Add new option to select
          const newOption = new Option(newText, newId, true, true);
          $select.append(newOption);

          console.log('📝 select-plus: Valor antes de setear:', $select.val());
          $select.val(newId).trigger('change');
          console.log('📝 select-plus: Valor después de setear:', $select.val());

          // Verificar que la opción existe
          const optionExists = $select.find('option[value="' + newId + '"]').length > 0;
          console.log('🔍 select-plus: ¿Opción existe?', optionExists);

          console.log('✅ select-plus: Elemento agregado al SELECT');

          // Call onCreateSuccess callback
          if (typeof onCreateSuccess === 'function') {
            onCreateSuccess({
              id: newId,
              label: newText,
              data: newItem
            });
          }
        }

        // Close modal
        $modal.modal('hide');

        // Clean up modal
        setTimeout(function() {
          $modalContainer.empty();
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open').css('overflow', '');
        }, 300);
      });

      // Clean up on modal close
      $modal.on('hidden.bs.modal', function() {
        $modalContainer.empty();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      });
    });

    // Expose destroy function
    $root.data('select-plus-destroy', function() {
      $select.off('change');
      $btnPlus.off('click');
      $modalContainer.off('formSaved');
      console.log('🗑️ select-plus: Componente destruido');
    });

    console.log('✅ select-plus: Componente listo');
  }

  /**
   * Load options from API
   */
  async function loadOptions($select, apiUrl, textField, valueField) {
    console.log('🔄 select-plus: Cargando opciones desde API...');

    try {
      $select.addClass('loading');

      const response = await fetch(apiUrl);
      if (!response.ok) {
        throw new Error('HTTP error! status: ' + response.status);
      }

      const json = await response.json();
      const items = (json.data && json.data.docs) || json.docs || json.data || json || [];

      console.log('📦 select-plus: ' + items.length + ' opciones recibidas');

      // Clear existing options except placeholder
      $select.find('option:not(:first)').remove();

      // Add options
      items.forEach(function(item) {
        const id = item[valueField] || item.id;
        const text = item[textField] || item.name || item.description || id;
        const option = new Option(text, id, false, false);
        $select.append(option);
      });

      $select.removeClass('loading').addClass('is-valid');
      console.log('✅ select-plus: Opciones cargadas exitosamente');

    } catch (error) {
      console.error('❌ select-plus: Error al cargar opciones:', error);
      $select.removeClass('loading').addClass('is-invalid');
    }
  }

  /**
   * Load initial options
   */
  async function loadInitialOptions($select, initialValue, initialData, apiUrl, textField, valueField) {
    console.log('🔄 select-plus: Cargando valores iniciales...');

    // If we have the data objects directly, use them
    if (initialData) {
      const dataArray = Array.isArray(initialData) ? initialData : [initialData];

      dataArray.forEach(function(item) {
        const id = item[valueField] || item.id;
        const text = item[textField] || item.name || item.description || id;

        // Check if option already exists
        let option = $select.find('option[value="' + id + '"]');
        if (option.length === 0) {
          option = new Option(text, id, true, true);
          $select.append(option);
        } else {
          option.prop('selected', true);
        }
      });

      $select.trigger('change');
      console.log('✅ select-plus: ' + dataArray.length + ' valor(es) inicial(es) cargado(s)');
      return;
    }

    // Otherwise, fetch by IDs
    if (!initialValue) {
      return;
    }

    const ids = Array.isArray(initialValue) ? initialValue : [initialValue];

    // For each ID, try to fetch or add directly
    for (let i = 0; i < ids.length; i++) {
      const id = ids[i];

      // Check if option already exists
      let option = $select.find('option[value="' + id + '"]');

      if (option.length === 0) {
        // Try to fetch from API
        try {
          const response = await fetch(apiUrl + '/' + id);
          if (response.ok) {
            const json = await response.json();
            const item = json.data || json;
            const text = item[textField] || item.name || item.description || id;
            option = new Option(text, id, true, true);
            $select.append(option);
          } else {
            // If fetch fails, add option with ID as text
            console.warn('⚠️ select-plus: No se pudo cargar ' + id + ', usando ID como texto');
            option = new Option(id, id, true, true);
            $select.append(option);
          }
        } catch (error) {
          console.error('❌ select-plus: Error al cargar ' + id + ':', error);
          // Add option with ID as text
          option = new Option(id, id, true, true);
          $select.append(option);
        }
      } else {
        option.prop('selected', true);
      }
    }

    $select.trigger('change');
    console.log('✅ select-plus: ' + ids.length + ' valor(es) inicial(es) cargado(s)');
  }

  /**
   * Public API
   */
  function getValue($root) {
    const $select = $root.find('.select-plus-select');
    return $select.val();
  }

  function setValue($root, value) {
    const $select = $root.find('.select-plus-select');
    $select.val(value).trigger('change');
  }

  function destroy($root) {
    const destroyFn = $root.data('select-plus-destroy');
    if (destroyFn) {
      destroyFn();
    }
  }

  function addOption($root, value, text, selected) {
    selected = selected !== undefined ? selected : false;
    const $select = $root.find('.select-plus-select');
    const option = new Option(text, value, selected, selected);
    $select.append(option);
    if (selected) {
      $select.trigger('change');
    }
  }

  function removeOption($root, value) {
    const $select = $root.find('.select-plus-select');
    $select.find('option[value="' + value + '"]').remove();
  }

  function clearOptions($root, keepPlaceholder) {
    keepPlaceholder = keepPlaceholder !== undefined ? keepPlaceholder : true;
    const $select = $root.find('.select-plus-select');
    if (keepPlaceholder) {
      $select.find('option:not(:first)').remove();
    } else {
      $select.empty();
    }
  }

  function loadOptionsFromAPI($root, apiUrl, textField, valueField) {
    textField = textField || 'name';
    valueField = valueField || 'id';
    const $select = $root.find('.select-plus-select');
    return loadOptions($select, apiUrl, textField, valueField);
  }

  // Registrar el componente
  if (!window.Components) {
    window.Components = {};
  }

  window.Components['select-plus'] = {
    init: init,
    getValue: getValue,
    setValue: setValue,
    destroy: destroy,
    addOption: addOption,
    removeOption: removeOption,
    clearOptions: clearOptions,
    loadOptionsFromAPI: loadOptionsFromAPI
  };

  window.select_plus = {
    init: init,
    getValue: getValue,
    setValue: setValue,
    destroy: destroy,
    addOption: addOption,
    removeOption: removeOption,
    clearOptions: clearOptions,
    loadOptionsFromAPI: loadOptionsFromAPI
  };

})(window);
