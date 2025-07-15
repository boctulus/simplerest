/*
    Bootstrap validation
*/

const setFormValidations = (validations) => {
    for (let field in validations) {

        const validation = validations[field].shift();
        const { error, error_detail } = validation;
        const field_selector = '#col-'+field;
        const feedback_selector = '#invalid-col-'+field;

        $(field_selector).removeClass('is-valid, is-invalid')

        if (error == false){
            $(field_selector).addClass('is-valid')
            $(feedback_selector).text('')
        } else {
            $(field_selector).addClass('is-invalid')
            $(feedback_selector).text(error_detail)
        }
    }
}

const clearFormValidations = () => {
    $('input').removeClass('is-valid')
    $('input').removeClass('is-invalid')
    $('.invalid-feedback').text('')
}

const clearForm = (formId) => {
    $(`#${formId}`).trigger('reset')
    clearFormValidations()
}

/**
 * Validates all forms matching a selector.
 * 
 * @param {Object} options
 * @param {string} [options.selector='form'] - CSS selector for the forms.
 * @param {function} [options.onInvalid] - Callback when validation fails. Receives (form, invalidFields).
 *
 * Ej de uso:
   
  document.addEventListener('DOMContentLoaded', () => {
    validateForms({
      selector: '.needs-validation',
      onInvalid: (form, fields) => {
        console.warn('Formulario inválido:', form);
        console.warn('Campos inválidos:', fields);
        // Mostrar mensaje personalizado
        alert('Por favor, completa todos los campos requeridos en este formulario.');
      }
    });
  });

 */
function validateForms({ selector = 'form', onInvalid } = {}) {
  const forms = document.querySelectorAll(selector);

  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      const requiredFields = form.querySelectorAll('[required]');
      const invalidFields = [];

      requiredFields.forEach(field => {
        if (!field.disabled && !field.value.trim()) {
          field.classList.add('is-invalid');
          invalidFields.push(field);
        } else {
          field.classList.remove('is-invalid');
        }
      });

      if (invalidFields.length > 0) {
        e.preventDefault();
        if (typeof onInvalid === 'function') {
          onInvalid(form, invalidFields);
        } else {
          alert('Por favor, completa todos los campos obligatorios.');
        }
      }
    });
  });
}

