<?php

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor/autoload.php'; // Path to main project vendor/autoload.php

use Boctulus\FriendlyposWeb\Helpers\DteDataTransformer;

// Test data as provided in the issue
$originalData = [
    "dteData" => [
        "Encabezado" => [
            "IdDoc" => [
                "TipoDTE" => 33,
                "Folio" => 0,
                "FchEmis" => "2025-12-01"
            ],
            "Emisor" => [
                "RUTEmisor" => "76795561-8",
                "RznSocEmisor" => "HAULMER SPA",
                "GiroEmisor" => "COMERCIO",
                "CdgSIISucur" => "81303347",
                "DirOrigen" => "Pasaje Los Pajaritos 8357",
                "CmnaOrigen" => "Santiago"
            ],
            "Receptor" => [
                "RUTRecep" => "99.500.840-8", // This has dots that should be removed
                "RznSocRecep" => "CAT ADMINISTRADORA DE TARJETAS S.A.",
                "GiroRecep" => "COMERCIO MINORISTA",
                "DirRecep" => "AV. VITACURA 2736 PISO13 1301",
                "CmnaRecep" => "Las Condes"
            ],
            "Totales" => [
                "MntNeto" => 548,
                "IVA" => 105,
                "MntTotal" => 653,
                "TotalPeriodo" => 653,
                "VlrPagar" => 653,
                "MntExe" => 0
            ]
        ],
        "Detalle" => [
            [
                "NroLinDet" => 1,
                "NmbItem" => "Producto sin nombre",
                "QtyItem" => 2,
                "PrcItem" => 25,
                "MontoItem" => 50
            ],
            [
                "NroLinDet" => 2,
                "NmbItem" => "Producto sin nombre",
                "QtyItem" => 6,
                "PrcItem" => 83,
                "MontoItem" => 498
            ]
        ]
    ],
    "responseOptions" => [
        "PDF",
        "FOLIO",
        "TIMBRE",
        "XML"
    ],
    "sendEmail" => null,
    "idempotencyKey" => "dte_1764603285463_ucocpyysx"
];

echo "Original DTE Data:\n";
echo json_encode($originalData["dteData"], JSON_PRETTY_PRINT) . "\n\n";

// Apply transformation
$transformedData = DteDataTransformer::transform($originalData["dteData"]);

echo "Transformed DTE Data:\n";
echo json_encode($transformedData, JSON_PRETTY_PRINT) . "\n\n";

// Verify changes
echo "Verification:\n";

// Check if RUTRecep has dots removed
$rutRecep = $transformedData["Encabezado"]["Receptor"]["RUTRecep"] ?? "NOT FOUND";
echo "RUTRecep after transformation: " . $rutRecep . "\n";
echo "RUTRecep dots removed: " . (strpos($rutRecep, '.') === false ? "YES" : "NO") . "\n\n";

// Check Emisor fields
$emisor = $transformedData["Encabezado"]["Emisor"] ?? [];
$hasRznSoc = array_key_exists("RznSoc", $emisor);
$hasRznSocEmisor = array_key_exists("RznSocEmisor", $emisor);
echo "Emisor has RznSoc (correct for invoices): " . ($hasRznSoc ? "YES" : "NO") . "\n";
echo "Emisor has RznSocEmisor (incorrect for invoices): " . ($hasRznSocEmisor ? "YES" : "NO") . "\n";

$hasGiroEmis = array_key_exists("GiroEmis", $emisor);
$hasGiroEmisor = array_key_exists("GiroEmisor", $emisor);
echo "Emisor has GiroEmis (correct for invoices): " . ($hasGiroEmis ? "YES" : "NO") . "\n";
echo "Emisor has GiroEmisor (incorrect for invoices): " . ($hasGiroEmisor ? "YES" : "NO") . "\n\n";

// Check Totales fields
$totales = $transformedData["Encabezado"]["Totales"] ?? [];
$hasTasaIVA = array_key_exists("TasaIVA", $totales);
echo "Totales has TasaIVA (correct for invoices): " . ($hasTasaIVA ? "YES" : "NO") . "\n";

$hasTotalPeriodo = array_key_exists("TotalPeriodo", $totales);
echo "Totales has TotalPeriodo (should be removed for invoices): " . ($hasTotalPeriodo ? "YES (REMAINING - PROBLEM)" : "NO (REMOVED - CORRECT)") . "\n";

$hasVlrPagar = array_key_exists("VlrPagar", $totales);
echo "Totales has VlrPagar (should be removed for invoices): " . ($hasVlrPagar ? "YES (REMAINING - PROBLEM)" : "NO (REMOVED - CORRECT)") . "\n";

$hasMntExe = array_key_exists("MntExe", $totales);
echo "Totales has MntExe (should be removed for invoices): " . ($hasMntExe ? "YES (REMAINING - PROBLEM)" : "NO (REMOVED - CORRECT)") . "\n";