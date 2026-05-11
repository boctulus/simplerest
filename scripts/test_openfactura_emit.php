<?php

/**
 * Test script para debugging del endpoint emit DTE
 *
 * Este script simula la request y captura todo lo que recibe el controlador
 */

require __DIR__ . '/../vendor/autoload.php';

use Boctulus\Simplerest\Core\Libs\Logger;

// Simular el ambiente HTTP
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';
$_SERVER['HTTP_ACCEPT'] = 'application/json';
$_SERVER['HTTP_X_API_KEY'] = '928e15a2d14d4a6292345f04960f4bd3';

// El JSON que estás enviando
$jsonData = '{
    "dteData": {
      "Encabezado": {
        "IdDoc": {
          "TipoDTE": 39,
          "Folio": 0,
          "FchEmis": "2025-11-30",
          "IndServicio": 3
        },
        "Emisor": {
          "RUTEmisor": "76070273-0",
          "RznSocEmisor": "Yevenes y cia spa",
          "GiroEmisor": "COMERCIO",
          "CdgSIISucur": "81303347",
          "DirOrigen": "Pasaje Los Pajaritos 8357",
          "CmnaOrigen": "Santiago Centro"
        },
        "Receptor": {
          "RUTRecep": "66666666-6"
        },
        "Totales": {
          "MntNeto": 16,
          "IVA": 3,
          "MntTotal": 19,
          "TotalPeriodo": 19,
          "VlrPagar": 19,
          "MntExe": 0
        }
      },
      "Detalle": [
        {
          "NroLinDet": 1,
          "NmbItem": "Producto sin nombre",
          "QtyItem": 2,
          "PrcItem": 8,
          "MontoItem": 16
        }
      ]
    },
    "responseOptions": [
      "PDF",
      "FOLIO",
      "TIMBRE"
    ],
    "sendEmail": null,
    "idempotencyKey": "dte_1764489605329_dtp5sodcr"
}';

// Simular php://input
// No podemos modificar directamente php://input en PHP, pero podemos crear un mock

echo "=== TEST DEBUG OPENFACTURA EMIT ===\n\n";

echo "1. JSON enviado:\n";
echo $jsonData . "\n\n";

echo "2. Decodificando JSON:\n";
$data = json_decode($jsonData, true);
var_dump($data);
echo "\n";

echo "3. Verificando campo dteData:\n";
echo "isset(\$data['dteData']): " . (isset($data['dteData']) ? 'true' : 'false') . "\n";
echo "is_array(\$data): " . (is_array($data) ? 'true' : 'false') . "\n";
echo "\n";

echo "4. Simulando request()->getBody(true):\n";

// Cargar el request object
$request = \Boctulus\Simplerest\Core\Request::getInstance();

// Obtener lo que recibiría el controlador
echo "Content-Type header: " . $request->getHeader('Content-Type') . "\n";
echo "Raw input: " . $request->getRaw() . "\n\n";

$bodyData = $request->getBody(true);
echo "getBody(true) result:\n";
var_dump($bodyData);
echo "\n";

if (is_array($bodyData)) {
    echo "Body es array: true\n";
    echo "isset(\$bodyData['dteData']): " . (isset($bodyData['dteData']) ? 'true' : 'false') . "\n";
} elseif (is_object($bodyData)) {
    echo "Body es object: true\n";
    echo "isset(\$bodyData->dteData): " . (isset($bodyData->dteData) ? 'true' : 'false') . "\n";
} else {
    echo "Body type: " . gettype($bodyData) . "\n";
}

echo "\n=== FIN TEST ===\n";
