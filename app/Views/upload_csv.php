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

    /*
        Ejemplo de uso
        
        let obj = {
            'MAYORISTA-CUSTOMER-10': '7000',
            'MAYORISTA-CUSTOMER-50': '6000',
            'MAYORISTA-L1-10': '6000',
            'MAYORISTA-L1-50': '5500',
            'MAYORISTA-L2-10': '5400',
            'MAYORISTA-L2-50': '4900',
        };

        const filteredObj = filterByKeys(obj, ['MAYORISTA-L1-10', 'MAYORISTA-L2-*']);
    */
    function filterByKeys(obj, keys) {
        const result = {};

        for (const key in obj) {
            if (keys.includes(key) || keys.includes('*')) {
            result[key] = obj[key];
            } else {
            for (const filterKey of keys) {
                if (filterKey.includes('*') && key.startsWith(filterKey.replace('*', ''))) {
                result[key] = obj[key];
                break;
                }
            }
            }
        }

        return result;
    }


    function convertToAssociativeArray(headers, rows) {
        const arrayOfObjects = [];

        rows.forEach(row => {
            const obj = {};

            row.forEach((value, index) => {
                const header = headers[index];
                obj[header] = value;
            });

            arrayOfObjects.push(obj);
        });

        return arrayOfObjects;
    }

    function handleFileSelect(event) {
        const file   = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const csvContent = e.target.result;
            
            const parsedData = processCSVContent(csvContent);
            // console.log(parsedData.headers); // ["Header 1", "Header 2", "Header 3"]
            // console.log(parsedData.rows);    // [["Value 1", "Value 2", "Value 3"]]

            const associativeArray = convertToAssociativeArray(parsedData.headers, parsedData.rows);

            // console.log(associativeArray);   // { "Header 1": "Value 1", "Header 2": "Value 2", "Header 3": "Value 3" }

            /*
                Aplicar filterByKeys() con ['MAYORISTA'] a cada array dentro de associativeArray
                y hacer un console.log del resultado
            */
           
            const filteredAssociativeArray = associativeArray.map(obj => filterByKeys(obj, ['ID', 'SKU', 'MAYORISTA-*']));

            filteredAssociativeArray.forEach(obj => console.log(obj));
        };

        reader.readAsText(file);
    }

    function processCSVContent(csvContent) {
        const results = Papa.parse(csvContent);
        const data = results.data;
        const lines = data.slice(1).map(line => line.map(value => value.trim()));
        const headers = data[0].map(header => header.replace(/\t/g, ''));

        // Eliminar filas con un solo campo vacío
        const filteredLines = lines.filter(line => line.some(value => value !== ''));

        return {
            headers: headers,
            rows: filteredLines
        };
    }


</script>