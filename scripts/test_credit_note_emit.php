<?php

/**
 * Test: Emisión de Nota de Crédito (DTE tipo 61) usando OpenFactura API
 *
 * Este script prueba la emisión de una Nota de Crédito usando el helper CreditNoteHelper
 * y el SDK de OpenFactura.
 *
 * Ejecutar con: php tests/test_credit_note_emit.php
 *
 * @author Pablo Bozzolo (boctulus)
 */

require_once __DIR__ . '/../app.php';

use Boctulus\FriendlyposWeb\Helpers\CreditNoteHelper;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;
use Boctulus\Simplerest\Core\Libs\Logger;

echo "\n========================================\n";
echo "TEST: Emisión de Nota de Crédito (DTE tipo 61)\n";
echo "========================================\n\n";

try {
    // 1. Leer configuración del .env
    $sandbox = filter_var(env('OPENFACTURA_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);

    // Usar API key correspondiente al modo
    $apiKey = $sandbox
        ? env('OPENFACTURA_API_KEY_DEV')
        : env('OPENFACTURA_API_KEY_PROD');

    if (empty($apiKey)) {
        throw new \Exception('API Key no configurada en .env. Verifica ' .
            ($sandbox ? 'OPENFACTURA_API_KEY_DEV' : 'OPENFACTURA_API_KEY_PROD'));
    }

    echo "1. Configuración\n";
    echo "   - Modo: " . ($sandbox ? 'SANDBOX (Desarrollo)' : 'PRODUCCIÓN') . "\n";
    echo "   - API Key: " . substr($apiKey, 0, 10) . "...\n";
    echo "   - Variable .env: OPENFACTURA_SANDBOX=" . ($sandbox ? 'true' : 'false') . "\n";
    echo "   - URL Base: " . ($sandbox ? 'https://dev-api.haulmer.com' : 'https://api.haulmer.com') . "\n\n";

    // 2. Crear el DTE de Nota de Crédito usando el helper
    echo "2. Creando estructura de DTE usando CreditNoteHelper\n";

    $params = [
        'fechaEmision' => date('Y-m-d'),
        'emisor' => [
            'RUTEmisor' => '76795561-8',
            'RznSoc' => 'HAULMER SPA',
            'GiroEmis' => 'COMERCIO',
            'Acteco' => 479110,
            'DirOrigen' => 'Pasaje Los Pajaritos 8357',
            'CmnaOrigen' => 'Santiago'
        ],
        'receptor' => [
            'RUTRecep' => '93834000-5',
            'RznSocRecep' => 'HAULMER SPA',
            'GiroRecep' => 'COMERCIO',
            'DirRecep' => 'Pasaje Los Pajaritos 8357',
            'CmnaRecep' => 'Santiago'
        ],
        'totales' => [
            'MntNeto' => 84,
            'TasaIVA' => 19,
            'IVA' => 16,
            'MntTotal' => 100
        ],
        'items' => [
            [
                'NmbItem' => 'AAAA',
                'QtyItem' => 1,
                'PrcItem' => 84,
                'MontoItem' => 84
            ]
        ],
        'referencia' => [
            'TpoDocRef' => 39,      // Tipo de documento que se está anulando (39 = Boleta)
            'FolioRef' => 631563,    // Folio del documento a anular
            'FchRef' => '2026-01-17',
            'CodRef' => 1,          // 1 = Anula, 2 = Corrige monto, 3 = Corrige texto
            'RazonRef' => 'Anulación de documento por solicitud del cliente', // La razón va aquí
            'IndGlobal' => 1        // Opcional: Indica si afecta a todo el documento
        ],
        'indNoRebaja' => true  // NOTA: RazonAnulacion NO va en IdDoc, va en Referencia->RazonRef
    ];

    $dteData = CreditNoteHelper::createFromParams($params);

    echo "   DTE creado exitosamente\n";
    echo "   - TipoDTE: " . $dteData['Encabezado']['IdDoc']['TipoDTE'] . " (Nota de Crédito)\n";
    echo "   - Fecha: " . $dteData['Encabezado']['IdDoc']['FchEmis'] . "\n";
    echo "   - Monto Total: $" . $dteData['Encabezado']['Totales']['MntTotal'] . "\n\n";

    // 3. Validar el DTE
    echo "3. Validando estructura del DTE\n";
    $validation = CreditNoteHelper::validate($dteData);

    if ($validation['valid']) {
        echo "   ✓ Validación exitosa\n\n";
    } else {
        echo "   ✗ Errores de validación:\n";
        foreach ($validation['errors'] as $error) {
            echo "     - $error\n";
        }
        echo "\n";
        exit(1);
    }

    // 4. Construir payload completo
    echo "4. Construyendo payload completo con CreditNoteHelper\n";

    $payload = CreditNoteHelper::buildPayload($dteData, [
        'responseOptions' => ['PDF', 'FOLIO', 'TIMBRE'],
        'customer' => [
            'fullName' => 'Cliente Ejemplo',
            'email' => 'cliente@ejemplo.cl'
        ],
        'origin' => 'TEST_SCRIPT'
    ]);

    echo "   Payload construido exitosamente\n";
    echo "   - Tiene 'response': " . (isset($payload['response']) ? 'SI' : 'NO') . "\n";
    echo "   - Tiene 'dte': " . (isset($payload['dte']) ? 'SI' : 'NO') . "\n";
    echo "   - Tiene 'customer': " . (isset($payload['customer']) ? 'SI' : 'NO') . "\n";
    echo "   - Tiene 'selfService': " . (isset($payload['selfService']) ? 'SI' : 'NO') . "\n";
    echo "   - Tiene 'custom': " . (isset($payload['custom']) ? 'SI' : 'NO') . "\n\n";

    // 5. Guardar payload para inspección
    $payloadFile = LOGS_PATH . 'credit_note_payload_' . date('YmdHis') . '.json';
    file_put_contents($payloadFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "5. Payload guardado en: $payloadFile\n\n";

    // 6. Inicializar SDK y emitir
    echo "6. Emitiendo Nota de Crédito\n";

    $sdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, false);

    $response = $sdk->emitirDTE(
        $payload['dte'],
        $payload['response'],
        $payload['custom'] ?? null,
        null, // sendEmail
        'test_nc_' . time(), // idempotencyKey
        $payload['customer'] ?? null,
        $payload['customizePage'] ?? null,
        $payload['selfService'] ?? null
    );

    echo "   Respuesta recibida\n\n";

    // 7. Procesar respuesta
    echo "7. Resultado\n";

    // Detectar error: si tiene error.code que comience con "OF-" o si falta FOLIO
    $hasError = isset($response['error']) || !isset($response['FOLIO']);

    if ($hasError) {
        echo "   ✗ ERROR:\n";

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
                } else {
                    echo "     " . json_encode($response['error']['details'], JSON_PRETTY_PRINT) . "\n";
                }
            }
        } else {
            echo "   - Respuesta sin FOLIO (posible error no estructurado)\n";

            dd(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'Payload enviado para diagnóstico:');
        }

        echo "\n   ⚠ La emisión FALLÓ\n";
    } else {
        echo "   ✓ ÉXITO:\n";

        if (isset($response['FOLIO'])) {
            echo "   - Folio: " . $response['FOLIO'] . "\n";
        }

        if (isset($response['TOKEN'])) {
            echo "   - Token: " . $response['TOKEN'] . "\n";
        }

        if (isset($response['PDF'])) {
            echo "   - PDF: Generado (base64)\n";
        }

        if (isset($response['TIMBRE'])) {
            echo "   - Timbre: Generado (base64)\n";
        }

        echo "\n   ✅ La emisión fue EXITOSA\n";
    }

    // 8. Guardar respuesta completa
    $responseFile = LOGS_PATH . 'credit_note_response_' . date('YmdHis') . '.json';
    file_put_contents($responseFile, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "\n   Respuesta completa guardada en: $responseFile\n";

    echo "\n========================================\n";
    echo "Test finalizado\n";
    echo "========================================\n\n";

} catch (\Exception $e) {
    echo "\n✗ ERROR FATAL:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n\n";
    exit(1);
}
