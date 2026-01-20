<?php

/**
 * Test: Emisión de Boleta Electrónica y su posterior anulación con Nota de Crédito
 *
 * Este script realiza un flujo completo:
 * 1. Emite una Boleta Electrónica (DTE tipo 39)
 * 2. Espera confirmación
 * 3. Emite una Nota de Crédito (DTE tipo 61) para anular la boleta
 *
 * Ejecutar con: php tests/test_boleta_y_nota_credito.php
 *
 * @author Pablo Bozzolo (boctulus)
 */

require_once __DIR__ . '/../app.php';

use Boctulus\FriendlyposWeb\Helpers\CreditNoteHelper;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;
use Boctulus\Simplerest\Core\Libs\Logger;

echo "\n========================================\n";
echo "TEST: Boleta Electrónica + Nota de Crédito\n";
echo "========================================\n\n";

try {
    // ==========================================
    // CONFIGURACIÓN
    // ==========================================
    echo "1. Configuración del Ambiente\n";
    echo "   " . str_repeat("-", 50) . "\n";

    $sandbox = filter_var(env('OPENFACTURA_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);
    $apiKey = $sandbox
        ? env('OPENFACTURA_API_KEY_DEV')
        : env('OPENFACTURA_API_KEY_PROD');

    if (empty($apiKey)) {
        throw new \Exception('API Key no configurada en .env. Verifica ' .
            ($sandbox ? 'OPENFACTURA_API_KEY_DEV' : 'OPENFACTURA_API_KEY_PROD'));
    }

    echo "   - Modo: " . ($sandbox ? 'SANDBOX (Desarrollo)' : 'PRODUCCIÓN') . "\n";
    echo "   - API Key: " . substr($apiKey, 0, 10) . "...\n";
    echo "   - URL Base: " . ($sandbox ? 'https://dev-api.haulmer.com' : 'https://api.haulmer.com') . "\n";
    echo "\n";

    // Inicializar SDK
    $sdk = OpenFacturaSDKFactory::make($apiKey, $sandbox, false);

    // ==========================================
    // PASO 1: EMITIR BOLETA ELECTRÓNICA (Tipo 39)
    // ==========================================
    echo "2. Emitiendo Boleta Electrónica (DTE tipo 39)\n";
    echo "   " . str_repeat("-", 50) . "\n";

    $fechaEmision = date('Y-m-d');

    // Datos de la boleta (estructura específica para tipo 39)
    $boletaData = [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => 39,  // Boleta Electrónica
                'Folio' => 0,     // 0 = El servidor asigna el folio
                'FchEmis' => $fechaEmision,
                'IndServicio' => 3  // 3 = Servicio
            ],
            'Emisor' => [
                'RUTEmisor' => '76795561-8',
                'RznSocEmisor' => 'HAULMER SPA',      // Para boletas: RznSocEmisor
                'GiroEmisor' => 'COMERCIO',            // Para boletas: GiroEmisor
                'CdgSIISucur' => '81303347',          // Para boletas: CdgSIISucur (NO Acteco)
                'DirOrigen' => 'Pasaje Los Pajaritos 8357',
                'CmnaOrigen' => 'Santiago'
            ],
            'Receptor' => [
                'RUTRecep' => '66666666-6',  // RUT genérico para boletas
                'RznSocRecep' => 'CLIENTE FINAL',
                // GiroRecep NO se usa en boletas
                'DirRecep' => 'Santiago',
                'CmnaRecep' => 'Santiago'
            ],
            'Totales' => [
                'MntNeto' => 42,          // 50 / 1.19 ≈ 42 (calculado desde MontoItem)
                'IVA' => 8,               // 50 - 42 = 8
                'MntTotal' => 50,         // Total con IVA
                'TotalPeriodo' => 50,     // Obligatorio para boletas
                'VlrPagar' => 50          // Obligatorio para boletas
            ]
        ],
        'Detalle' => [
            [
                'NroLinDet' => 1,
                'NmbItem' => 'Producto de prueba',
                'QtyItem' => 1,
                'PrcItem' => 50,          // Precio CON IVA incluido
                'MontoItem' => 50         // Monto CON IVA incluido (QtyItem * PrcItem)
            ]
        ]
    ];

    echo "   Datos de la boleta:\n";
    echo "   - Tipo: 39 (Boleta Electrónica)\n";
    echo "   - Fecha: $fechaEmision\n";
    echo "   - Monto Neto: $42\n";
    echo "   - IVA: $8 (19%)\n";
    echo "   - Monto Total: $50\n";
    echo "   - Item: Producto de prueba (precio CON IVA: $50)\n\n";

    echo "   Enviando a OpenFactura...\n";

    // Emitir boleta
    $boletaResponse = $sdk->emitirDTE(
        $boletaData,
        ['PDF', 'FOLIO', 'TIMBRE'],
        ['origin' => 'TEST_BOLETA_NC'],
        null, // sendEmail
        'boleta_' . time() // idempotencyKey
    );

    // Si la respuesta es un string JSON, parsearlo
    if (is_string($boletaResponse)) {
        $boletaResponse = json_decode($boletaResponse, true);
    }

    // Guardar respuesta de la boleta
    $boletaFile = LOGS_PATH . 'boleta_response_' . date('YmdHis') . '.json';
    file_put_contents($boletaFile, json_encode($boletaResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Verificar si la boleta se emitió correctamente
    $boletaExitosa = isset($boletaResponse['FOLIO']) && !isset($boletaResponse['error']);

    if (!$boletaExitosa) {
        echo "\n   ✗ ERROR AL EMITIR BOLETA:\n";

        if (isset($boletaResponse['error'])) {
            echo "   - Mensaje: " . ($boletaResponse['error']['message'] ?? 'N/A') . "\n";
            echo "   - Código: " . ($boletaResponse['error']['code'] ?? 'N/A') . "\n";

            if (isset($boletaResponse['error']['details'])) {
                echo "   - Detalles:\n";
                if (is_array($boletaResponse['error']['details'])) {
                    foreach ($boletaResponse['error']['details'] as $detail) {
                        if (is_array($detail)) {
                            echo "     * Campo: " . ($detail['field'] ?? 'N/A') . "\n";
                            echo "       Issue: " . ($detail['issue'] ?? 'N/A') . "\n";
                        }
                    }
                } else {
                    echo "     " . json_encode($boletaResponse['error']['details'], JSON_PRETTY_PRINT) . "\n";
                }
            }
        } else {
            echo "   - Respuesta sin FOLIO\n";
        }

        echo "\n   Respuesta guardada en: $boletaFile\n";
        echo "\n   ⚠ No se puede continuar sin una boleta válida.\n";
        echo "\n========================================\n";
        echo "Test finalizado CON ERRORES\n";
        echo "========================================\n\n";
        exit(1);
    }

    // Boleta emitida exitosamente
    $folioBoletaEmitida = $boletaResponse['FOLIO'];
    $tokenBoleta = $boletaResponse['TOKEN'] ?? null;

    echo "\n   ✅ BOLETA EMITIDA EXITOSAMENTE:\n";
    echo "   - Folio: $folioBoletaEmitida\n";
    if ($tokenBoleta) {
        echo "   - Token: $tokenBoleta\n";
    }
    if (isset($boletaResponse['PDF'])) {
        echo "   - PDF: Generado\n";
    }
    if (isset($boletaResponse['TIMBRE'])) {
        echo "   - Timbre: Generado\n";
    }
    echo "\n   Respuesta guardada en: $boletaFile\n";
    echo "\n";

    // ==========================================
    // PAUSA ANTES DE EMITIR LA NOTA DE CRÉDITO
    // ==========================================
    echo "3. Preparando Nota de Crédito\n";
    echo "   " . str_repeat("-", 50) . "\n";
    echo "   Se va a anular la boleta con folio: $folioBoletaEmitida\n";
    echo "   Esperando 2 segundos...\n";
    sleep(2);
    echo "\n";

    // ==========================================
    // PASO 2: EMITIR NOTA DE CRÉDITO (Tipo 61)
    // ==========================================
    echo "4. Emitiendo Nota de Crédito (DTE tipo 61)\n";
    echo "   " . str_repeat("-", 50) . "\n";

    // Usar CreditNoteHelper para construir la NC
    // NOTA: La NC debe tener los mismos datos de la boleta que anula
    $paramsNC = [
        'fechaEmision' => date('Y-m-d'),
        'emisor' => [
            'RUTEmisor' => '76795561-8',
            'RznSoc' => 'HAULMER SPA',        // NC usa RznSoc (no RznSocEmisor)
            'GiroEmis' => 'COMERCIO',          // NC usa GiroEmis (no GiroEmisor)
            'Acteco' => 479110,
            'DirOrigen' => 'Pasaje Los Pajaritos 8357',
            'CmnaOrigen' => 'Santiago'
        ],
        'receptor' => [
            'RUTRecep' => '66666666-6',
            'RznSocRecep' => 'CLIENTE FINAL',
            'GiroRecep' => 'VARIOS',           // NC sí puede tener GiroRecep
            'DirRecep' => 'Santiago',
            'CmnaRecep' => 'Santiago'
        ],
        'totales' => [
            'MntNeto' => 50,          // En NC: precio SIN IVA
            'TasaIVA' => 19,
            'IVA' => 10,              // 50 * 0.19 ≈ 10 (redondeado)
            'MntTotal' => 60          // 50 + 10 = 60
        ],
        'items' => [
            [
                'NmbItem' => 'Producto de prueba',
                'QtyItem' => 1,
                'PrcItem' => 50,      // En NC: precio SIN IVA (base)
                'MontoItem' => 50     // En NC: monto SIN IVA (base)
            ]
        ],
        'referencia' => [
            'TpoDocRef' => 39,                          // Tipo de documento a anular (Boleta)
            'FolioRef' => $folioBoletaEmitida,          // Folio REAL de la boleta emitida
            'FchRef' => $fechaEmision,                  // Fecha de la boleta
            'CodRef' => 1,                              // 1 = Anula documento
            'RazonRef' => 'Anulación de boleta de prueba',
            'IndGlobal' => 1
        ],
        'indNoRebaja' => true
    ];

    echo "   Construyendo DTE con CreditNoteHelper...\n";
    $ncDteData = CreditNoteHelper::createFromParams($paramsNC);

    // Validar
    $validation = CreditNoteHelper::validate($ncDteData);
    if (!$validation['valid']) {
        echo "\n   ✗ ERRORES DE VALIDACIÓN:\n";
        foreach ($validation['errors'] as $error) {
            echo "     - $error\n";
        }
        exit(1);
    }

    echo "   ✓ Validación exitosa\n";

    // Construir payload completo
    $payloadNC = CreditNoteHelper::buildPayload($ncDteData, [
        'responseOptions' => ['PDF', 'FOLIO', 'TIMBRE'],
        'customer' => [
            'fullName' => 'Cliente Final',
            'email' => 'cliente@ejemplo.cl'
        ],
        'origin' => 'TEST_BOLETA_NC'
    ]);

    echo "\n   Datos de la Nota de Crédito:\n";
    echo "   - Tipo: 61 (Nota de Crédito)\n";
    echo "   - Anula Folio: $folioBoletaEmitida (Boleta tipo 39)\n";
    echo "   - Fecha: " . date('Y-m-d') . "\n";
    echo "   - Monto Neto: $50 (base SIN IVA)\n";
    echo "   - IVA: $10 (19%)\n";
    echo "   - Monto Total: $60\n";
    echo "   - Razón: Anulación de boleta de prueba\n\n";

    echo "   Enviando a OpenFactura...\n";

    // Guardar payload para inspección
    $payloadNCFile = LOGS_PATH . 'nota_credito_payload_' . date('YmdHis') . '.json';
    file_put_contents($payloadNCFile, json_encode($payloadNC, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Emitir Nota de Crédito
    $ncResponse = $sdk->emitirDTE(
        $payloadNC['dte'],
        $payloadNC['response'],
        $payloadNC['custom'] ?? null,
        null, // sendEmail
        'nc_' . time(), // idempotencyKey
        $payloadNC['customer'] ?? null,
        $payloadNC['customizePage'] ?? null,
        $payloadNC['selfService'] ?? null
    );

    // Si la respuesta es un string JSON, parsearlo
    if (is_string($ncResponse)) {
        $ncResponse = json_decode($ncResponse, true);
    }

    // Guardar respuesta
    $ncFile = LOGS_PATH . 'nota_credito_response_' . date('YmdHis') . '.json';
    file_put_contents($ncFile, json_encode($ncResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Verificar si la NC se emitió correctamente
    $ncExitosa = isset($ncResponse['FOLIO']) && !isset($ncResponse['error']);

    echo "\n";

    if (!$ncExitosa) {
        echo "   ✗ ERROR AL EMITIR NOTA DE CRÉDITO:\n";

        if (isset($ncResponse['error'])) {
            echo "   - Mensaje: " . ($ncResponse['error']['message'] ?? 'N/A') . "\n";
            echo "   - Código: " . ($ncResponse['error']['code'] ?? 'N/A') . "\n";

            if (isset($ncResponse['error']['details'])) {
                echo "   - Detalles:\n";
                if (is_array($ncResponse['error']['details'])) {
                    foreach ($ncResponse['error']['details'] as $detail) {
                        if (is_array($detail)) {
                            echo "     * Campo: " . ($detail['field'] ?? 'N/A') . "\n";
                            echo "       Issue: " . ($detail['issue'] ?? 'N/A') . "\n";
                        }
                    }
                } else {
                    echo "     " . json_encode($ncResponse['error']['details'], JSON_PRETTY_PRINT) . "\n";
                }
            }
        } else {
            echo "   - Respuesta sin FOLIO\n";
        }

        echo "\n   Payload enviado guardado en: $payloadNCFile\n";
        echo "   Respuesta guardada en: $ncFile\n";
        echo "\n";
    } else {
        $folioNC = $ncResponse['FOLIO'];
        $tokenNC = $ncResponse['TOKEN'] ?? null;

        echo "   ✅ NOTA DE CRÉDITO EMITIDA EXITOSAMENTE:\n";
        echo "   - Folio NC: $folioNC\n";
        if ($tokenNC) {
            echo "   - Token: $tokenNC\n";
        }
        if (isset($ncResponse['PDF'])) {
            echo "   - PDF: Generado\n";
        }
        if (isset($ncResponse['TIMBRE'])) {
            echo "   - Timbre: Generado\n";
        }

        echo "\n   Payload enviado guardado en: $payloadNCFile\n";
        echo "   Respuesta guardada en: $ncFile\n";
        echo "\n";
    }

    // ==========================================
    // RESUMEN FINAL
    // ==========================================
    echo "========================================\n";
    echo "RESUMEN DEL TEST\n";
    echo "========================================\n\n";

    echo "✅ Boleta Electrónica (tipo 39):\n";
    echo "   - Folio: $folioBoletaEmitida\n";
    echo "   - Estado: EMITIDA\n";
    echo "   - Archivo: $boletaFile\n\n";

    if ($ncExitosa) {
        echo "✅ Nota de Crédito (tipo 61):\n";
        echo "   - Folio: $folioNC\n";
        echo "   - Anula Folio: $folioBoletaEmitida\n";
        echo "   - Estado: EMITIDA\n";
        echo "   - Archivo: $ncFile\n\n";

        echo "🎉 PROCESO COMPLETADO EXITOSAMENTE\n";
        echo "   La boleta fue emitida y anulada correctamente.\n";
    } else {
        echo "❌ Nota de Crédito (tipo 61):\n";
        echo "   - Estado: ERROR\n";
        echo "   - Archivo: $ncFile\n\n";

        echo "⚠️ PROCESO COMPLETADO CON ERRORES\n";
        echo "   La boleta se emitió pero la NC falló.\n";
    }

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
