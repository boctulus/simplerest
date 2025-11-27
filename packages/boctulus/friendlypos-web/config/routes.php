<?php

use Boctulus\Simplerest\Core\WebRouter;

/*
    friendlypos_web Package Web Routes

    Rutas para generación de PDFs de comprobantes FriendlyPOS
*/

// // Generar PDF de venta por ID
// WebRouter::get('pdf/venta/{id}', 'Boctulus\FriendlyposWeb\Controllers\ComprobantePdfController@generarPdf');

// // Generar PDF forzando tipo de documento
// WebRouter::get('pdf/venta/{id}/tipo/{tipo}', 'Boctulus\FriendlyposWeb\Controllers\ComprobantePdfController@generarPdfEspecial');

// // Generar ticket de venta
// WebRouter::post('pdf/ticket', 'Boctulus\FriendlyposWeb\Controllers\ComprobantePdfController@generarTicketPdf');

// // Rutas de testing
// WebRouter::get('pdf/test', 'Boctulus\FriendlyposWeb\Controllers\ComprobantePdfController@testBoletaPdf');
// WebRouter::get('pdf/ventas/listar', 'Boctulus\FriendlyposWeb\Controllers\ComprobantePdfController@listarVentas');

/*
    OpenFactura API Routes

    Rutas para integración con OpenFactura (facturación electrónica Chile)
*/
WebRouter::group('api/openfactura', function() {
    // Emisión de DTE
    WebRouter::post('dte/emit', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@emitDTE');

    // Consulta de estado de DTE
    WebRouter::get('dte/status/{token}', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getDTEStatus');

    // Anulación de guía de despacho
    WebRouter::post('dte/anular-guia', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@anularGuiaDespacho');

    // Anulación general de DTE (por medio de Nota de Crédito)
    WebRouter::post('dte/anular', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@anularDTE');

    // Consulta de contribuyente por RUT
    WebRouter::get('taxpayer/{rut}', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getTaxpayer');

    // Información de la organización
    WebRouter::get('organization', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getOrganization');

    // Registro de ventas
    WebRouter::get('sales-registry/{year}/{month}', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getSalesRegistry');

    // Registro de compras
    WebRouter::get('purchase-registry/{year}/{month}', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getPurchaseRegistry');

    // Obtener documento específico
    WebRouter::get('document/{rut}/{type}/{folio}', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@getDocument');

    // Health check
    WebRouter::get('health', 'Boctulus\FriendlyposWeb\Controllers\OpenFacturaController@health');
});
