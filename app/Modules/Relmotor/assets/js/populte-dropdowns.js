$(document).ready(function() {
    // Inicializa Select2 en el select
    $('#woo_cat_prd').select2({
    placeholder: "Seleccionar",
    allowClear: true,
    data: [
        { id: 'al', text: 'Alternador' },
        { id: 're', text: 'Regulador' },
        { id: 'bc', text: 'Bocina' }
    ]
    });

    $('select[data-id="pa_sistema-electrico"]').select2({
    placeholder: "Seleccionar",
    allowClear: true,
    data: [
        { id: 'de', text: 'DENSO' },
        { id: 'iv', text: 'IVECO' },
        { id: 'fd', text: 'FORD' }
    ]
    });

    $('select[data-id="pa_marca"]').select2({
    placeholder: "Seleccionar",
    allowClear: true,
    data: [
        { id: 'dn', text: 'DNI' },
        { id: 'gs', text: 'GAUSS' },
        { id: 'ap', text: 'AS PARTS' }
    ]
    });
});
