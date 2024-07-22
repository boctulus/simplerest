/*
  Corrección de implementación trunca de <input type="number"> donde el max=""
  y min="" se restringen tanto con las flechas como al ingresar por teclado

  @author Pablo Bozzolo < boctulus@gmail.com >
*/

document.addEventListener('DOMContentLoaded', function() {
  const inputQuery = document.querySelectorAll('input[type="number"][max], input[type="number"][min], input[type="number"][step]');

  function roundToStep(value, step, min = 0, round_fn = Math.ceil) {
    const invStep = 1 / step;
    return round_fn((value - min) * invStep) / invStep + min;
  }

  function countDecimals(value) {
    if (Math.floor(value) === value) return 0;
    return value.toString().split(".")[1].length || 0;
  }

  inputQuery.forEach(function(input) {
    let timer;

    input.addEventListener('keyup', function() {
      clearTimeout(timer);

      timer = setTimeout(function() {
        const currentValue = parseFloat(input.value);
        const min = parseFloat(input.getAttribute('min'));
        const max = parseFloat(input.getAttribute('max'));
        const step = input.getAttribute('step');

        if (!isNaN(currentValue)) {
          let newValue = currentValue;
          let decimals = 0;

          if (step) {
            decimals = countDecimals(parseFloat(step));
            newValue = roundToStep(newValue, parseFloat(step));
          }

          if (!isNaN(min) && newValue < min) {
            newValue = min;
          } else if (!isNaN(max) && newValue > max) {
            newValue = max;
          }

          if (newValue !== currentValue) {
            input.value = newValue.toFixed(decimals);

            // Bloqueo el control por 300 ms para evitar errores por parte del usuario
            input.setAttribute('readonly', 'readonly');

            setTimeout(function() {
              input.removeAttribute('readonly');
            }, 300);
          }
        }
      }, 750);
    });
  });
});