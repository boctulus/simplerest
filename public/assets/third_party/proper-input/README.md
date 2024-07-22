# ProperInput

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

Simply include the ProperInput.js file in your project.
Basic Usage

```html
<input type="number" id="myInput" min="0" max="100" step="0.5">

<script src="ProperInput.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const properInput = new ProperInput();
        properInput.init();
    });
</script>
```

# API

Constructor

```javascript
const properInput = new ProperInput(roundFn);
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

# Use Cases

1. Input with Decimal Step

```html
<input type="number" min="0" max="10" step="0.1">
```
ProperInput ensures that entered values adhere to the 0.1 step, rounding correctly (e.g., 3.14 will round to 3.1).


2. Range with Negative Values
```html
<input type="number" min="-50" max="50" step="5">
```
Correctly handles ranges including both negative and positive values, ensuring values align with the step of 5.

3. Large Values with Significant Step
```html
<input type="number" min="1000000" max="9999999" step="111111">
```

Ideal for scenarios requiring large increments within wide ranges, such as budget or population selection.

4. Precise Percentage Input
```html
<input type="number" min="0" max="100" step="0.01">
```

Perfect for percentages requiring two-decimal precision, like interest rates or discounts.

5. Year Selector
```html
<input type="number" min="1900" max="2100" step="1">
```

Useful for selecting years, ensuring only valid years within the specified range are entered.


# Contributing

Everyone is welcome to contribute. Please open an issue to discuss major changes before submitting a pull request.

# License

MIT License