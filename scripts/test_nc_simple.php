<?php

/**
 * Test Simple: Emisión directa de Nota de Crédito para anular boleta 631909
 *
 * Este script prueba la emisión de una NC con la estructura mínima
 * basada en el ejemplo exitoso de OpenFacturaSDKTestController
 *
 * @author Pablo Bozzolo (boctulus)
 */

require_once __DIR__ . '/../app.php';

use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;

echo "\n========================================\n";
echo "TEST SIMPLE: Nota de Crédito\n";
echo "========================================\n\n";

try {
    // Configuración
    $sandbox = filter_var(env('OPENFACTURA_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);
    $apiKey = $sandbox
        ? env('OPENFACTURA_API_KEY_DEV')
        : env('OPENFACTURA_API_KEY_PROD');

    echo "Modo: " . ($sandbox ? 'SANDBOX' : 'PRODUCCIÓN') . "\n";
    echo "API Key: " . substr($apiKey, 0, 10) . "...\n\n";

    $sdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, false);

    // Folio de la boleta a anular
    $folioBoletaAAnular = 631909;
    $fechaBoletaAAnular = '2026-01-20';

    echo "Emitiendo Nota de Crédito para anular Boleta #$folioBoletaAAnular\n\n";

    // Estructura mínima basada en el ejemplo del controlador
    $dteData = [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => 61,
                'Folio' => 0,  // 0 = El servidor asigna
                'FchEmis' => date('Y-m-d')
            ],
            'Emisor' => [
                'RUTEmisor' => '76795561-8',
                'RznSoc' => 'HAULMER SPA',
                'GiroEmis' => 'VENTA AL POR MENOR EN EMPRESAS DE VENTA A DISTANCIA VÍA INTERNET',
                'Acteco' => 479100,
                'DirOrigen' => 'Pasaje Los Pajaritos 8357',
                'CmnaOrigen' => 'Santiago'
            ],
            'Receptor' => [
                'RUTRecep' => '66666666-6',
                'RznSocRecep' => 'CLIENTE FINAL',
                'GiroRecep' => 'VARIOS',
                'DirRecep' => 'Santiago',
                'CmnaRecep' => 'Santiago'
            ],
            'Totales' => [
                'MntNeto' => 50,
                'TasaIVA' => 19,
                'IVA' => 10,
                'MntTotal' => 60
            ]
        ],
        'Detalle' => [
            [
                'NroLinDet' => 1,
                'NmbItem' => 'Producto de prueba',
                'QtyItem' => 1,
                'PrcItem' => 50,
                'MontoItem' => 50
            ]
        ],
        'Referencia' => [
            [
                'NroLinRef' => 1,
                'TpoDocRef' => 39,                    // Tipo de documento a anular (Boleta)
                'FolioRef' => $folioBoletaAAnular,    // Folio de la boleta a anular
                'FchRef' => $fechaBoletaAAnular,      // Fecha de la boleta
                'CodRef' => 1                         // 1 = Anula documento
            ]
        ]
    ];

    echo "Payload DTE:\n";
    echo json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    // Emitir NC SIN campos adicionales (customer, selfService, etc.)
    $response = $sdk->emitirDTE(
        $dteData,
        ['PDF', 'FOLIO', 'TIMBRE'],
        ['origin' => 'TEST_NC_SIMPLE'],
        null, // sendEmail
        'nc_simple_' . time() // idempotencyKey
    );

    // Parsear si es string JSON
    if (is_string($response)) {
        $response = json_decode($response, true);
    }

    // Guardar request
    $requestFile = LOGS_PATH . 'nc_simple_request_' . date('YmdHis') . '.json';
    file_put_contents($requestFile, json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Guardar respuesta
    $responseFile = LOGS_PATH . 'nc_simple_response_' . date('YmdHis') . '.json';
    file_put_contents($responseFile, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    echo "Respuesta:\n";

    if (isset($response['FOLIO']) && !isset($response['error'])) {
        echo "✅ ÉXITO:\n";
        echo "   - Folio NC: " . $response['FOLIO'] . "\n";
        echo "   - Token: " . ($response['TOKEN'] ?? 'N/A') . "\n";
        echo "   - PDF: " . (isset($response['PDF']) ? 'Generado' : 'No') . "\n";
        echo "   - Timbre: " . (isset($response['TIMBRE']) ? 'Generado' : 'No') . "\n";
    } else {
        echo "✗ ERROR:\n";
        if (isset($response['error'])) {
            echo "   - Mensaje: " . ($response['error']['message'] ?? 'N/A') . "\n";
            echo "   - Código: " . ($response['error']['code'] ?? 'N/A') . "\n";

            if (isset($response['error']['details'])) {
                echo "   - Detalles:\n";
                if (is_array($response['error']['details'])) {
                    foreach ($response['error']['details'] as $detail) {
                        if (is_array($detail)) {
                            echo "     * Campo: " . ($detail['field'] ?? 'N/A') . "\n";
                            echo "       Issue: " . ($detail['issue'] ?? 'N/A') . "\n";
                        }
                    }
                }
            }
        } else {
            echo "   - Respuesta sin FOLIO ni error específico\n";
            echo "   - Respuesta completa: " . json_encode($response, JSON_UNESCAPED_UNICODE) . "\n";
        }
    }

    echo "\nArchivo de respuesta: $responseFile\n";

    echo "\n========================================\n";
    echo "Test finalizado\n";
    echo "========================================\n\n";

} catch (\Exception $e) {
    echo "\n✗ ERROR FATAL:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    exit(1);
}
