<?php

    js_file('third_party/proper-input/proper-input.js', null, true);

?>

<style>
    fieldset {
        margin-bottom: 20px;
        border: 1px solid #ccc;
        padding: 10px;
    }
    label {
        display: inline-block;
        width: 150px;
    }
    .description {
        font-style: italic;
        margin-bottom: 5px;
    }
</style>

<h1>Pruebas de ProperInput</h1>

<fieldset>
    <legend>Caso 1: Básico</legend>
    <div class="description">min: 0, max: 100, step: 1</div>
    <label for="input1">Número (0-100):</label>
    <input id="input1" type="number" min="0" max="100" step="1">
</fieldset>

<fieldset>
    <legend>Caso 2: Decimales</legend>
    <div class="description">min: 0, max: 10, step: 0.1</div>
    <label for="input2">Decimal (0-10):</label>
    <input id="input2" type="number" min="0" max="10" step="0.1">
</fieldset>

<fieldset>
    <legend>Caso 3: Negativo a Positivo</legend>
    <div class="description">min: -50, max: 50, step: 5</div>
    <label for="input3">Número (-50 a 50):</label>
    <input id="input3" type="number" min="-50" max="50" step="5">
</fieldset>

<fieldset>
    <legend>Caso 4: Solo Mínimo</legend>
    <div class="description">min: 100, step: 10</div>
    <label for="input4">Número (min 100):</label>
    <input id="input4" type="number" min="100" step="10">
</fieldset>

<fieldset>
    <legend>Caso 5: Solo Máximo</legend>
    <div class="description">max: 1000, step: 50</div>
    <label for="input5">Número (max 1000):</label>
    <input id="input5" type="number" max="1000" step="50">
</fieldset>

<fieldset>
    <legend>Caso 6: Paso Grande</legend>
    <div class="description">min: 0, max: 1000000, step: 10000</div>
    <label for="input6">Número Grande:</label>
    <input id="input6" type="number" min="0" max="1000000" step="10000">
</fieldset>

<fieldset>
    <legend>Caso 7: Decimales Pequeños</legend>
    <div class="description">min: 0, max: 1, step: 0.01</div>
    <label for="input7">Decimal Pequeño:</label>
    <input id="input7" type="number" min="0" max="1" step="0.01">
</fieldset>

<fieldset>
    <legend>Caso 8: Rango Estrecho</legend>
    <div class="description">min: 99, max: 101, step: 0.1</div>
    <label for="input8">Rango Estrecho:</label>
    <input id="input8" type="number" min="99" max="101" step="0.1">
</fieldset>

<fieldset>
    <legend>Caso 9: Solo Step</legend>
    <div class="description">step: 3.14</div>
    <label for="input9">Múltiplos de Pi:</label>
    <input id="input9" type="number" step="3.14">
</fieldset>

<fieldset>
    <legend>Caso 10: Valores Grandes</legend>
    <div class="description">min: 1000000, max: 9999999, step: 111111</div>
    <label for="input10">`INPUT`:</label>
    <input id="input10" type="number" min="1000000" max="9999999" step="111111">
</fieldset>


<fieldset>
    <legend>Caso 11: Min y Step con decimales</legend>
    <div class="description">min: 25.5, step: 8,5</div>
    <label for="input11">INPUT:</label>
    <input id="input11" type="number" step="8.5" min="25.5">
</fieldset>

<fieldset>
    <legend>Caso 12: Min y Step con decimales II</legend>
    <div class="description">min: 2.28, step: 0.76</div>
    <label for="input12">INPUT:</label>
    <input id="input12" type="number" step="0.76" min="2.28">
</fieldset>

<script>
    // Uso:
    const properInput = new ProperInput();

    properInput.setSelector('input#input12');

    // Para cambiar la función de redondeo (por ejemplo, a Math.round):
    // properInput.setRoundFunction(Math.round);

    properInput.enforceStep(false);

    // Arranque
    properInput.init();

</script>