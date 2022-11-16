
/*
 * Editor client script for DB table part_numbers
 * Created by http://editor.datatables.net/generator
 */

window.addEventListener('DOMContentLoaded', (event) => {
    (function($){

        $(document).ready(function() {
            var editor = new $.fn.dataTable.Editor( {
                ajax: 'php/table.part_numbers.php',
                table: '#part_numbers',
                fields: [
                    {
                        "label": "nombre:",
                        "name": "nombre"
                    },
                    {
                        "label": "nota:",
                        "name": "nota"
                    },
                    {
                        "label": "created_at:",
                        "name": "created_at",
                        "type": "datetime",
                        "format": "ddd, D MMM YY"
                    },
                    {
                        "label": "updated_at:",
                        "name": "updated_at",
                        "type": "datetime",
                        "format": "ddd, D MMM YY"
                    }
                ]
            } );
        
            var table = $('#part_numbers').DataTable( {
                dom: 'Bfrtip',
                ajax: 'php/table.part_numbers.php',
                columns: [
                    {
                        "data": "nombre"
                    },
                    {
                        "data": "nota"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "updated_at"
                    }
                ],
                select: true,
                lengthChange: false,
                buttons: [
                    { extend: 'create', editor: editor },
                    { extend: 'edit',   editor: editor },
                    { extend: 'remove', editor: editor }
                ]
            } );
        } );
        
    }(jQuery));
    
});
