<?php
    css_file('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css');

    js_file ('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js');      
    js_file ('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js');
    js_file ('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/lint/javascript-lint.min.js');
?>

<style>
.json-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: auto;
    padding: 36px 0 36px;
    background: #282a36;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

#copy-button {
    position: absolute;
    top: 2px;
    right: 10px;
    background: #6272a4;
    color: #f8f8f2;
    border: none;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
}

#json-content {
    display: none;
}
</style>


<div class="json-container">
    <button id="copy-button" class="btn btn-sm">
        <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" class="octicon octicon-copy">
            <path d="M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z"></path>
            <path d="M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z"></path>
        </svg>
    </button>
    <textarea id="json-content"></textarea>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // JSON data
    const jsonData = {
        "name": "John Doe",
        "age": 30,
        "city": "NY city"
    };

    // Initialize CodeMirror
    const codeMirrorInstance = CodeMirror.fromTextArea(document.getElementById('json-content'), {
        mode: "application/json",
        lineNumbers: true,
        theme: "dracula",
        readOnly: true //
    });

    // Set JSON content
    codeMirrorInstance.setValue(JSON.stringify(jsonData, null, 2));

    // Copy to clipboard function
    document.getElementById('copy-button').addEventListener('click', function () {
        const jsonContent = codeMirrorInstance.getValue();
        
        // Crear un elemento textarea temporal
        const tempTextArea = document.createElement('textarea');
        tempTextArea.value = jsonContent;
        document.body.appendChild(tempTextArea);
        
        // Seleccionar y copiar el contenido
        tempTextArea.select();
        document.execCommand('copy');
        
        // Eliminar el textarea temporal
        document.body.removeChild(tempTextArea);
        
        // alert('JSON copied to clipboard!');
    });
});

</script>