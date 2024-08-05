# ProperInput

ProperInput is a lightweight and efficient JavaScript class designed to enhance the functionality of `<input type="number">` elements in web forms. It addresses common limitations of these inputs, especially regarding the application of `min`, `max`, and `step` attributes.

In the native implementation of INPUT type="number", the min, max and step are not really forced. There is no validation!

Now it's possible to really *force* min, max and step follow the rules!

... and the best part is it works with the native `<input type="number">` element.

## Author

Pablo Bozzolo (boctulus) <boctulus@gmail.com>

## Rationale

Many existing libraries for handling numeric inputs are heavy, complex, or don't adequately handle all use cases. 

ProperInput aims to provide a streamlined solution for numeric inputs, focusing on:

1. Consistent enforcement of the `step` attribute for both manual and automatic value changes.
2. Precise handling of decimal values across various scenarios.
3. Accurate application of `min` and `max` limits in all cases.
4. Optimized performance, especially on mobile devices and pages with multiple inputs.

These features ensure a reliable and efficient user experience, addressing common challenges in numeric input handling.

## Features

- Correctly applies `min`, `max`, and `step` attributes.
- Properly handles both decimal and integer values.
- Allows customization of the rounding function.
- Works with multiple inputs on a page.
- Optimized performance with delayed input processing.

## Installation

Simply include the `proper-input.js` file in your project.


```html
<script src="proper-input.js"></script>

<input type="number" id="myInput" min="0" max="100" step="0.5">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const properInput = new ProperInput();
        properInput.init();
    });
</script>
```

## Basic usage

Create an instance, then set up if it's needed and init.

```javascript
// Create instance:
const properInput = new ProperInput();

// Change rounding funcion if you need it (i.e. to Math.round):
properInput.setRoundFunction(Math.round);

// Set a different CSS selector:
const customProperInput = new ProperInput('input.custom-number-input');

// Init
properInput.init();
```

Important note

Let's suppose you have two inputs:

```html
<input type="number" min="50" max="100" step="0.01">
<input type="number" min="1" max="5" step="1">
```

You need to suscribe to the value change can ocurr because this library: 

```javascript
$('#numMeters, #numBoxes').on('input change', function() {
    // do something with updated values
});
```

### API

Constructor

```javascript
const properInput = new ProperInput(selector = null, roundFn = null);
```

Other methods

```javascript
setSelector(selector): Sets a custom selector for the inputs.
```

```javascript
setRoundFunction(fn): Sets a custom rounding function.
```

```javascript
init(): Initializes the functionality on all selected inputs.
```

## Advanced Features

### Controlling Step Enforcement

ProperInput allows you to dynamically control whether the `step` attribute is enforced or not, using the `enforceStep()` method.

This way it's possible to disable the step when a value is entered by the user in the <INPUT>, making it valid only when it gets increased/decreased. This prevents a 100 from being rounded to 99, for example, if the step is 33 with a min of zero.

```javascript
enforceStep(value): Enables or disables step enforcement.
```

Usage:

```javascript
const properInput = new ProperInput();
properInput.init();

// Disable step enforcement
properInput.enforceStep(false);

// Re-enable step enforcement
properInput.enforceStep(true);
```

When step enforcement is disabled, the input will still respect the min and max attributes, but will allow any value within that range, regardless of the step attribute.

This feature is particularly useful in scenarios where you want to temporarily allow more flexible input, such as during initial data entry, while still maintaining the ability to enforce the step when needed.

## Full example

```html
<input type="number" id="numBoxes"   min="3"   step="1">
<input type="number" id="numMeters"  min="2.1" step="0.7">
<input type="number" name="quantity" max="50"  step="1">
```

```javascript
const properInputNumMeters = new ProperInput('#numMeters');
properInputNumMeters.setRoundFunction(Math.round);
properInputNumMeters.enforceStep(false);
properInputNumMeters.init();

const properInputNumBoxes = new ProperInput('#numBoxes');
properInputNumBoxes.setRoundFunction(Math.round);
properInputNumBoxes.init();

const properInputWooQty = new ProperInput('input[name="quantity"]');
properInputWooQty.setRoundFunction(Math.round);
properInputWooQty.init();    
```


## Contributing

Everyone is welcome to contribute. Please open an issue to discuss major changes before submitting a pull request.

## License

MIT License