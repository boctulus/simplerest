<h3>CSV import</h3>

<span>Por favor seleccione el archivo .csv</span>
<p></p>

<form>
    <input type="file" id="csvFileInput" accept=".csv">
</form>

<?php
    js_file("https://cdn.jsdelivr.net/npm/papaparse@5.3.1/papaparse.min.js");
?>

<script>
    const csvFileInput = document.getElementById('csvFileInput');

    csvFileInput.addEventListener('change', handleFileSelect, false);

    function handleFileSelect(event) {
        const file   = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const csvContent = e.target.result;
            
            const parsedData = processCSVContent(csvContent);
            console.log(parsedData.headers); // ["Header 1", "Header 2", "Header 3"]
            console.log(parsedData.data); // [["Value 1", "Value 2", "Value 3"]]
        };

        reader.readAsText(file);
    }

    function processCSVContent(csvContent) {
        const results = Papa.parse(csvContent);
        const data = results.data;
        const lines = data.slice(1).map(line => line.toString().split(','));
        const headers = data[0].map(header => header.replace(/\t/g, ''));

        return {
            headers: headers,
            data: lines
        };
    }


</script>