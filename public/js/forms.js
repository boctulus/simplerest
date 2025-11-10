/**
 * Form Helper Functions
 *
 * Funciones helper para trabajar con formularios
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */

(function(window) {
  'use strict';

  /**
   * Obtiene los datos de un formulario como objeto
   *
   * @param {jQuery} formElem - Elemento del formulario
   * @param {boolean} use_id - Si usar id o name
   * @param {string|null} prefix - Prefijo a remover de los nombres
   * @returns {object} Objeto con los datos del formulario
   */
  function getFormData(formElem, use_id, prefix) {
    use_id = use_id !== undefined ? use_id : false;
    prefix = prefix || null;

    const jsonData = {};
    const skippedFields = [];

    formElem.find(':input').each(function(_, input) {
      const $input = $(input);
      let name = use_id ? input.id : input.name;
      let value = input.value;

      // Skip inputs without a name or id
      if (!name || name.trim() === '') {
        return; // Continue to next iteration
      }

      if (prefix != null && name.startsWith(prefix)) {
        name = name.substr(prefix.length);
      }

      // Skip empty values only for non-required fields that aren't select elements
      if (value === '' && !input.required && input.type !== 'select-one') {
        skippedFields.push(name);
        return;
      }

      // Type casting based on input type and field characteristics
      const castedValue = castFormValue(input, value);

      jsonData[name] = castedValue;
    });

    if (skippedFields.length > 0) {
      console.log('⏭️  Campos salteados (vacíos y no requeridos):', skippedFields);
    }

    return jsonData;
  }

  /**
   * Cast form values to appropriate types
   *
   * @param {HTMLElement} input - Input element
   * @param {*} value - Value to cast
   * @returns {*} Casted value
   */
  function castFormValue(input, value) {
    const $input = $(input);
    const inputType = input.type;
    const fieldName = input.name || input.id;

    // Handle select-multiple (including select2)
    if (inputType === 'select-multiple' || $input.prop('multiple')) {
      const selectedValues = $input.val();
      return Array.isArray(selectedValues) ? selectedValues : [];
    }

    // Para selects vacíos, enviar explícitamente null
    if (inputType === 'select-one' && (value === '' || value == null)) {
      return null;
    }

    if (value === '' || value === null || value === undefined) {
      return null;
    }

    if (inputType === 'select-one' && (value === 'true' || value === 'false')) {
      return value === 'true';
    }

    // Boolean fields by naming convention
    if (fieldName && (fieldName.startsWith('is_') || fieldName.startsWith('has_') ||
                     fieldName.startsWith('can_') || fieldName.startsWith('should_') ||
                     fieldName.includes('_active') || fieldName.includes('_enabled') ||
                     fieldName.includes('_verified'))) {
      if (value === 'true' || value === '1' || value === 'on') return true;
      if (value === 'false' || value === '0' || value === 'off') return false;
    }

    // Numeric fields
    if (inputType === 'number' || inputType === 'range') {
      const num = parseFloat(value);
      return isNaN(num) ? null : num;
    }

    // Date fields
    if (inputType === 'date') {
      return new Date(value + 'T00:00:00.000Z');
    }

    // DateTime fields
    if (inputType === 'datetime-local') {
      return new Date(value);
    }

    // Time fields
    if (inputType === 'time') {
      return value;
    }

    // Checkbox fields
    if (inputType === 'checkbox') {
      return input.checked;
    }

    // Radio fields
    if (inputType === 'radio' && input.checked) {
      return value;
    }

    if (inputType === 'radio' && !input.checked) {
      return undefined;
    }

    // Fields that should be numeric based on naming
    if (fieldName && fieldName !== 'store_id' &&
        (fieldName.includes('_id') || fieldName.includes('age') ||
         fieldName.includes('count') || fieldName.includes('total') ||
         fieldName.includes('amount') || fieldName.includes('price') ||
         fieldName.includes('limit') || fieldName.includes('quantity'))) {
      const num = parseFloat(value);
      if (!isNaN(num)) return num;
    }

    // Default: return as string (trimmed)
    return value.trim();
  }

  /**
   * Rellena un formulario con datos
   *
   * @param {object} data_obj - Objeto con los datos
   * @param {string|null} prefix - Prefijo para los ids
   */
  function fillForm(data_obj, prefix) {
    prefix = prefix || null;

    if (typeof data_obj !== 'object') {
      return;
    }

    for (const key in data_obj) {
      if (!data_obj.hasOwnProperty(key)) continue;

      const value = data_obj[key];
      const selector = '#' + (prefix == null ? '' : prefix) + key;
      const inputElem = document.querySelector(selector);

      if (!inputElem) {
        continue;
      }

      const tag = inputElem.tagName;
      let formattedValue = formatValueForForm(value, inputElem);

      if (tag === 'INPUT' || tag === 'TEXTAREA') {
        inputElem.value = formattedValue;
      } else if (tag === 'SELECT') {
        // Convert boolean values to string for select options
        if (typeof value === 'boolean') {
          formattedValue = value.toString();
        }

        const optionExists = Array.from(inputElem.options).some(function(opt) {
          return opt.value === formattedValue;
        });

        if (optionExists) {
          inputElem.value = formattedValue;
          inputElem.dispatchEvent(new Event('change'));
        } else {
          console.warn('Option with value "' + formattedValue + '" not found in SELECT "' + selector + '".');
        }
      } else {
        console.warn('Element with selector "' + selector + '" is not a supported form field.');
      }
    }
  }

  /**
   * Format values for form display
   *
   * @param {*} value - Value to format
   * @param {HTMLElement} inputElem - Input element
   * @returns {string} Formatted value
   */
  function formatValueForForm(value, inputElem) {
    if (value === null || value === undefined) {
      return '';
    }

    const inputType = inputElem.type;

    // Date formatting
    if (inputType === 'date' && value instanceof Date) {
      return value.toISOString().split('T')[0];
    }

    // DateTime formatting
    if (inputType === 'datetime-local' && value instanceof Date) {
      return value.toISOString().slice(0, 16);
    }

    // Time formatting
    if (inputType === 'time' && value instanceof Date) {
      return value.toTimeString().slice(0, 5);
    }

    // Boolean for checkboxes
    if (inputType === 'checkbox' && typeof value === 'boolean') {
      inputElem.checked = value;
      return value;
    }

    // Convert everything else to string
    return value.toString();
  }

  // Exportar como global
  window.getFormData = getFormData;
  window.castFormValue = castFormValue;
  window.fillForm = fillForm;
  window.formatValueForForm = formatValueForForm;

})(window);
