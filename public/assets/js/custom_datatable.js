/*
    La clase CustomDataTable permite crear una tabla HTML dinámica, controlando la configuración de cabeceras (<th>), las filas de datos, y el "partial rendering" de filas sin volver a renderizar toda la tabla. 
    
    Esta implementación es compatible con Bootstrap 5, permitiendo personalizar clases CSS para cabeceras y columnas, y actualizando columnas específicas de forma eficiente. 

    La mayoría de las implementaciones estándar de DataTables, como jQuery DataTables o DataTables.net, no soportan directamente la actualización parcial de filas sin volver a renderizar la fila completa.
    
    Las opciones comunes para manipular datos en estas bibliotecas incluyen métodos como row().data() para actualizar la fila, pero este enfoque típicamente requiere el redibujado completo de la fila, lo que puede afectar el rendimiento en tablas grandes o cuando se necesita una actualización precisa sin perder el estado de celdas específicas (como clases CSS aplicadas dinámicamente).

    @author Pablo Bozzolo

    Version 1.2.0

    Inicializacion:

    // Para una tabla donde el ID está en el campo 'id_product'
    const table = new CustomDataTable('miTabla', 'id_product', headers);

    // Para una tabla con ID en el campo 'ID'
    const table2 = new CustomDataTable('otraTabla', 'ID', headers);
    
*/

/*
    CustomDataTable permite crear una tabla HTML dinámica, controlando la configuración 
    de cabeceras (<th>), las filas de datos, y el "partial rendering" de filas sin 
    volver a renderizar toda la tabla.
    
    Esta implementación es compatible con Bootstrap 5, permitiendo personalizar 
    clases CSS para cabeceras y columnas, y actualizando columnas específicas 
    de forma eficiente.

    @author Pablo Bozzolo
    Version 1.2.0    
*/

class CustomDataTable {
    constructor(tableId, rowIdField, headers) {
        this.table = document.getElementById(tableId);
        this.rowIdField = rowIdField;
        this.headers = headers;
        this.rows = new Map();
        
        // Estado de visibilidad de columnas
        this.columnVisibility = new Map();
        this.headers.forEach(header => {
            this.columnVisibility.set(header.key, {
                visible: true,
                hideBreakpoints: header.hideBreakpoints || []
            });
        });

        this.initTable();
        this.applyAllColumnVisibility();
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

    // Aplicar visibilidad a todas las columnas
    applyAllColumnVisibility() {
        this.headers.forEach(header => {
            this.applyColumnVisibility(header.key);
        });
    }

    // Mostrar/ocultar una columna
    toggleColumnVisibility(columnKey, visible) {
        const columnIndex = this.headers.findIndex(h => h.key === columnKey);
        if (columnIndex === -1) {
            console.error(`Columna '${columnKey}' no encontrada`);
            return;
        }

        // Actualizar estado interno
        const visibility = this.columnVisibility.get(columnKey);
        if (!visibility) {
            console.error(`Estado de visibilidad no encontrado para columna '${columnKey}'`);
            return;
        }

        visibility.visible = visible;
        this.applyColumnVisibility(columnKey);
    }

    // Aplicar visibilidad y reglas responsive a una columna
    applyColumnVisibility(columnKey) {
        const columnIndex = this.headers.findIndex(h => h.key === columnKey);
        if (columnIndex === -1) return;

        const visibility = this.columnVisibility.get(columnKey);
        if (!visibility) return;

        const th = this.table.querySelector(`th:nth-child(${columnIndex + 1})`);
        const cells = this.table.querySelectorAll(`td:nth-child(${columnIndex + 1})`);
        const elements = [th, ...cells];

        // Remover clases de visibilidad existentes
        elements.forEach(el => {
            el.classList.remove('d-none', 'd-sm-table-cell', 'd-md-table-cell', 
                             'd-lg-table-cell', 'd-xl-table-cell', 'd-xxl-table-cell');
        });

        // Si la columna está marcada como no visible, ocultarla
        if (!visibility.visible) {
            elements.forEach(el => el.classList.add('d-none'));
            return;
        }

        // Aplicar reglas responsive si existen
        if (visibility.hideBreakpoints && visibility.hideBreakpoints.length > 0) {
            const showAtBreakpoint = this.getShowBreakpoint(visibility.hideBreakpoints);
            elements.forEach(el => {
                el.classList.add('d-none', `d-${showAtBreakpoint}-table-cell`);
            });
        }
    }

    // Determinar breakpoint para mostrar la columna
    getShowBreakpoint(hideBreakpoints) {
        const breakpoints = ['sm', 'md', 'lg', 'xl', 'xxl'];
        const highestHiddenBreakpoint = hideBreakpoints.reduce((highest, current) => {
            return breakpoints.indexOf(current) > breakpoints.indexOf(highest) ? current : highest;
        });
        
        const nextBreakpointIndex = breakpoints.indexOf(highestHiddenBreakpoint) + 1;
        return breakpoints[nextBreakpointIndex] || 'xxl';
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
        newRow.dataset.rowId = rowData[this.rowIdField];

        this.headers.forEach(header => {
            const cell = newRow.insertCell();
            cell.classList.add(...(header.cssClasses || []));
            
            if (rowData[header.key] !== undefined) {
                cell.innerHTML = rowData[header.key];
            }
        });

        this.rows.set(rowData[this.rowIdField], newRow);
    }

    // Actualizar una fila existente
    updateRow(rowData) {
        const existingRow = this.rows.get(rowData[this.rowIdField]);
        
        if (!existingRow) {
            console.error(`Row con ${this.rowIdField}=${rowData[this.rowIdField]} no encontrada`);
            return;   
        }

        this.headers.forEach((header, index) => {
            if (rowData[header.key] !== undefined) {
                existingRow.cells[index].innerHTML = rowData[header.key];
            }
        });
    }

    // Obtener una fila por ID
    getRow(id) {
        return this.rows.get(id);
    }

    // Eliminar una fila por ID
    removeRow(id) {
        const row = this.rows.get(id);
        if (row) {
            row.remove();
            this.rows.delete(id);
        }
    }

    // Limpiar toda la tabla (excepto cabeceras)
    clear() {
        const tbody = this.table.tBodies[0];
        tbody.innerHTML = '';
        this.rows.clear();
    }
}