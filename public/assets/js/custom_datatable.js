/*
    La clase CustomDataTable permite crear una tabla HTML dinámica, controlando la configuración de cabeceras (<th>), las filas de datos, y el "partial rendering" de filas sin volver a renderizar toda la tabla. 
    
    Esta implementación es compatible con Bootstrap 5, permitiendo personalizar clases CSS para cabeceras y columnas, y actualizando columnas específicas de forma eficiente. 

    La mayoría de las implementaciones estándar de DataTables, como jQuery DataTables o DataTables.net, no soportan directamente la actualización parcial de filas sin volver a renderizar la fila completa.
    
    Las opciones comunes para manipular datos en estas bibliotecas incluyen métodos como row().data() para actualizar la fila, pero este enfoque típicamente requiere el redibujado completo de la fila, lo que puede afectar el rendimiento en tablas grandes o cuando se necesita una actualización precisa sin perder el estado de celdas específicas (como clases CSS aplicadas dinámicamente).

    @author Pablo Bozzolo

    Version 1.1.0

    Inicializacion:

    // Para una tabla donde el ID está en el campo 'id_product'
    const table = new CustomDataTable('miTabla', 'id_product', headers);

    // Para una tabla con ID en el campo 'ID'
    const table2 = new CustomDataTable('otraTabla', 'ID', headers);
    
*/

class CustomDataTable {
    constructor(tableId, rowIdField, headers) {
        this.table = document.getElementById(tableId);
        this.rowIdField = rowIdField; // Nuevo parámetro para el nombre del campo ID
        this.headers = headers;
        this.rows = new Map(); // Mantiene un registro de todas las filas por ID

        // Inicializar la tabla con las cabeceras
        this.initTable();
    }

    // Inicializar tabla con cabecera HTML completa
    initTable() {
        this.table.innerHTML = '';
        
        const thead = this.table.createTHead();
        const headerRow = thead.insertRow();

        this.headers.forEach(header => {
            const th = document.createElement('th');
            th.classList.add(...(header.cssClasses || []));
            th.innerHTML = header.htmlContent;
            headerRow.appendChild(th);
        });

        this.table.createTBody();
    }

    // Método set mejorado que acepta múltiples filas
    set(...rowsData) {
        rowsData.forEach(rowData => {
            if (!rowData[this.rowIdField]) {
                throw new Error(`Cada fila debe tener un '${this.rowIdField}' único.`);
            }
            
            if (this.rows.has(rowData[this.rowIdField])) {
                this.updateRow(rowData);
            } else {
                this.addRow(rowData);
            }
        });
    }

    // Agregar una nueva fila
    addRow(rowData) {
        const tbody = this.table.tBodies[0];
        const newRow = tbody.insertRow();
        newRow.dataset.rowId = rowData[this.rowIdField]; // Usar el campo ID especificado

        // Crear celdas basadas en las cabeceras definidas
        this.headers.forEach(header => {
            const cell = newRow.insertCell();
            cell.classList.add(...(header.cssClasses || []));
            
            // Establecer el contenido si existe
            if (rowData[header.key] !== undefined) {
                cell.innerHTML = rowData[header.key];
            }
        });

        // Guardar referencia a la fila
        this.rows.set(rowData[this.rowIdField], newRow);
    }

    // Actualizar una fila existente
    updateRow(rowData) {
        const existingRow = this.rows.get(rowData[this.rowIdField]);
        
        if (!existingRow) {
            console.log(`ERROR. Row con ${this.rowIdField}=${rowData[this.rowIdField]} no encontrada`, rowData);
            return;   
        }

        // Actualizar solo las celdas que tienen nuevos datos
        this.headers.forEach((header, index) => {
            if (rowData[header.key] !== undefined) {
                existingRow.cells[index].innerHTML = rowData[header.key];
            }
        });
    }

    // Método para obtener una fila por ID
    getRow(id) {
        return this.rows.get(id);
    }

    // Método para eliminar una fila por ID
    removeRow(id) {
        const row = this.rows.get(id);
        if (row) {
            row.remove();
            this.rows.delete(id);
        }
    }

    // Método para limpiar toda la tabla (excepto cabeceras)
    clear() {
        const tbody = this.table.tBodies[0];
        tbody.innerHTML = '';
        this.rows.clear();
    }
}