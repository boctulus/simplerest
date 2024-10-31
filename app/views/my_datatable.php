<h3 class="mt-3 text-primary">MY DATATABLE</h3>

<!-- 
    Datatabla dinamica 

     https://chatgpt.com/c/67227701-d6b8-800d-9fcd-7485330cdf19   
-->
<table id="exampleTable" class="table table-striped table-hover mt-3"></table>


<script>
    // CustomDataTable.js - A dynamic, Bootstrap 5-compatible JavaScript DataTable

class CustomDataTable {
    constructor(tableId, headers) {
        this.table = document.getElementById(tableId);
        this.headers = headers;
        this.rows = new Map();

        // Initialize table with headers
        this.initTable();
    }

    // Initialize table with header row
    initTable() {
        const thead = this.table.createTHead();
        const headerRow = thead.insertRow();

        this.headers.forEach(header => {
            const th = document.createElement('th');
            th.textContent = header.name;
            th.classList.add(...(header.cssClasses || []));
            headerRow.appendChild(th);
        });

        this.table.createTBody(); // Create empty tbody for rows
    }

    // Add or update rows with partial updates supported
    set(...rows) {
        rows.forEach(rowData => {
            const { id } = rowData;
            if (!id) throw new Error("Each row must have an 'id' field.");

            // Check if row already exists
            if (this.rows.has(id)) {
                // Update existing row with new data
                this.updateRow(rowData);
            } else {
                // Add new row if not present
                this.addRow(rowData);
            }
        });
    }

    // Add a new row with the given data
    addRow(rowData) {
        const tbody = this.table.tBodies[0];
        const newRow = tbody.insertRow();
        newRow.dataset.id = rowData.id;

        this.headers.forEach(header => {
            const td = newRow.insertCell();
            td.textContent = rowData[header.key] || '';
            td.classList.add(...(header.cssClasses || []));
        });

        this.rows.set(rowData.id, newRow); // Track row for future updates
    }

    // Update an existing row with new data, without re-rendering the entire row
    updateRow(rowData) {
        const existingRow = this.rows.get(rowData.id);

        this.headers.forEach(header => {
            const cellIndex = this.headers.findIndex(h => h.key === header.key);
            if (cellIndex !== -1 && rowData[header.key] !== undefined) {
                const cell = existingRow.cells[cellIndex];
                cell.textContent = rowData[header.key];
            }
        });
    }
}


document.addEventListener("DOMContentLoaded", (event) => {
  // Example usage:
    // 1. Initialize the DataTable with headers
    const datatable = new CustomDataTable('exampleTable', [
        { name: 'ID', key: 'id', cssClasses: ['text-center'] },
        { name: 'SKU', key: 'sku', cssClasses: ['text-uppercase', 'fw-bold'] },
        { name: 'Featured Image', key: 'featured_img', cssClasses: ['img-thumbnail'] },
        // Add more columns as needed
    ]);

    // 2. Add rows dynamicallys
    datatable.set(
        { id: 1, sku: 'ASDF100' },
        { id: 2, sku: 'PUFF304' }
    );

    // 3. Update specific columns without re-rendering the entire row
    datatable.set(
        { id: 1, featured_img: 'http://image1.jpg' },
        { id: 2, featured_img: 'http://image2.jpg' }
    );
});



</script>