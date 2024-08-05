/*
    ProperInput ver 1.1

    Corrección de implementación trunca de <input type="number"> donde el max=""
    y min="" se restringen tanto con las flechas como al ingresar por teclado

    @author Pablo Bozzolo < boctulus@gmail.com >

    // Uso:
    const properInput = new ProperInput();

    // Para cambiar la función de redondeo (por ejemplo, a Math.round):
    properInput.setRoundFunction(Math.round);

    // Para desactivar la aplicación del paso:
    properInput.enforceStep(false);

    // Arranque
    properInput.init();

    // Para usar un selector diferente:
    const customProperInput = new ProperInput('input.custom-number-input');

    // ...
*/

class ProperInput 
{
  constructor(selector = null, roundFn = null) {
    this.roundFn = roundFn || Math.round;
    this.inputs = [];
    this.enforceStepEnabled = true;
    this.setSelector(selector);        
  }

  setSelector(selector = 'input[type="number"][max], input[type="number"][min], input[type="number"][step]') {
    this.inputSelector = selector;
  }

  setRoundFunction(fn) {
    if (typeof fn === 'function') {
      this.roundFn = fn;
    } else {
      throw new Error('El argumento debe ser una función');
    }
  }

  enforceStep(value = true) {
    this.enforceStepEnabled = value;
  }

  roundToStep(value, step, min = 0) {
    const steps = this.roundFn((value - min) / step);
    return min + (steps * step);
  }

  processValue(value, min, max, step) {
    if (isNaN(value)) return NaN;
    let newValue = value;
    
    if (!isNaN(step) && this.enforceStepEnabled) {
      const effectiveMin = !isNaN(min) ? min : Math.floor(value / step) * step;
      newValue = this.roundToStep(newValue, step, effectiveMin);
    }
    if (!isNaN(min) && newValue < min) {
      newValue = min;
    } else if (!isNaN(max) && newValue > max) {
      newValue = max;
    }
    return newValue;
  }

  // Not in use
  initializeValue(input, delay) {
    setTimeout(() => {
      const currentValue = parseFloat(input.value);
      const min          = parseFloat(input.getAttribute('min'));
      const max          = parseFloat(input.getAttribute('max'));
      const step         = parseFloat(input.getAttribute('step'));
      const newValue     = this.processValue(currentValue, min, max, step);

      if (!isNaN(newValue) && newValue !== currentValue) {
        input.value = newValue.toFixed(this.countDecimals(step));
      }
    }, delay);
  }

  init() {
    if (typeof document !== 'undefined') {
      this.inputs = document.querySelectorAll(this.inputSelector);
      
      this.inputs.forEach(input => {
        this.setupInput(input);
        // this.initializeValue(input, 500);
      });

    }
  }

  setupInput(input) {
    let timer;
    input.addEventListener('keyup', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        this.handleInput(input);
      }, 750);
    });

    // Procesar el valor inicial
    this.handleInput(input);
  }

  handleInput(input) {
    const currentValue = parseFloat(input.value);
    const min          = parseFloat(input.getAttribute('min'));
    const max          = parseFloat(input.getAttribute('max'));
    const step         = parseFloat(input.getAttribute('step'));
    const newValue     = this.processValue(currentValue, min, max, step);
    
    if (!isNaN(newValue) && newValue !== currentValue) {
      input.value = newValue.toFixed(this.countDecimals(step));
      this.temporarilyDisableInput(input);
    }
  }

  countDecimals(value) {
    if (Math.floor(value) === value) 
      return 0;
    
    return value.toString().split(".")[1].length || 0;
  }

  temporarilyDisableInput(input) {
    input.setAttribute('readonly', 'readonly');
    setTimeout(() => {
      input.removeAttribute('readonly');
    }, 300);
  }
}