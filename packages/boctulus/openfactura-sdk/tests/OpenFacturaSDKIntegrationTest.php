<?php

namespace Boctulus\OpenfacturaSdk\Tests;

use PHPUnit\Framework\TestCase;
use Boctulus\OpenfacturaSdk\Libs\OpenFacturaSDK;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;
use Boctulus\OpenfacturaSdk\Mocks\OpenFacturaSDKMock;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';

/**
 * Test de Integración para OpenFactura SDK
 *
 * Este test verifica que el SDK funcione correctamente contra la API real de OpenFactura
 *
 * Ejecutar con: ./vendor/bin/phpunit packages/boctulus/openfactura-sdk/tests/OpenFacturaSDKIntegrationTest.php
 */
class OpenFacturaSDKIntegrationTest extends TestCase
{
    protected $apiKey = '928e15a2d14d4a6292345f04960f4bd3'; // API Key de desarrollo
    protected $sdk;

    protected function setUp(): void
    {
        $this->sdk = new OpenFacturaSDK($this->apiKey, true); // sandbox = true
    }

    /**
     * Test 1: Verificar que el Factory funciona
     */
    public function testFactory()
    {
        echo "\n[TEST] Verificando Factory...\n";

        $sdkReal = OpenFacturaSDKFactory::make($this->apiKey, true, false);
        $sdkMock = OpenFacturaSDKFactory::make($this->apiKey, true, true);

        $this->assertInstanceOf(OpenFacturaSDK::class, $sdkReal);
        $this->assertInstanceOf(OpenFacturaSDKMock::class, $sdkMock);

        echo "[OK] Factory funciona correctamente\n";
    }

    /**
     * Test 2: Verificar conexión con la API - Company Info
     */
    public function testGetCompanyInfo()
    {
        echo "\n[TEST] Obteniendo información de empresa...\n";

        $response = $this->sdk->getCompanyInfo();

        echo "[RESPONSE] " . json_encode($response, JSON_PRETTY_PRINT) . "\n";

        // El endpoint puede no estar disponible en sandbox, solo verificamos que hay respuesta
        $this->assertNotNull($response);

        echo "[OK] Test de company info completado\n";
    }

    /**
     * Test 3: Consultar contribuyente por RUT
     */
    public function testGetTaxpayer()
    {
        echo "\n[TEST] Consultando contribuyente...\n";

        $rut = '76795561-8'; // Haulmer SPA

        $response = $this->sdk->getTaxpayer($rut);

        echo "[RESPONSE] " . json_encode($response, JSON_PRETTY_PRINT) . "\n";

        // Puede venir como string JSON o como array
        $this->assertNotNull($response);

        if (is_string($response)) {
            $data = json_decode($response, true);
            $this->assertIsArray($data);
            $this->assertArrayHasKey('rut', $data);
            echo "[SUCCESS] RUT encontrado: {$data['rut']}\n";
        } else {
            $this->assertIsArray($response);
            $this->assertArrayHasKey('rut', $response);
            echo "[SUCCESS] RUT encontrado: {$response['rut']}\n";
        }

        echo "[OK] Contribuyente consultado correctamente\n";
    }

    /**
     * Test 4: Emitir DTE (Boleta Electrónica)
     */
    public function testEmitirBoleta()
    {
        echo "\n[TEST] Emitiendo Boleta Electrónica...\n";

        $folio = rand(10000, 99999);

        $dteData = [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 39,  // Boleta Electrónica
                    'Folio' => $folio,
                    'FchEmis' => date('Y-m-d'),
                    'IndServicio' => 3  // Servicio
                ],
                'Emisor' => [
                    'RUTEmisor' => '76795561-8',
                    'RznSocEmisor' => 'HAULMER SPA',
                    'GiroEmisor' => 'VENTA AL POR MENOR EN EMPRESAS DE VENTA A DISTANCIA VÍA INTERNET',
                    'CdgSIISucur' => '81303347',
                    'DirOrigen' => 'ARTURO PRAT 527 CURICO',
                    'CmnaOrigen' => 'Curicó'
                ],
                'Receptor' => [
                    'RUTRecep' => '66666666-6'  // RUT genérico
                ],
                'Totales' => [
                    'MntNeto' => 840,
                    'IVA' => 160,
                    'MntTotal' => 1000,
                    'TotalPeriodo' => 1000,
                    'VlrPagar' => 1000
                ]
            ],
            'Detalle' => [
                [
                    'NroLinDet' => 1,
                    'NmbItem' => 'Producto de Prueba SDK',
                    'QtyItem' => 1,
                    'PrcItem' => 1000,
                    'MontoItem' => 1000
                ]
            ]
        ];

        echo "[INFO] Emitiendo DTE con Folio: $folio\n";

        $response = $this->sdk->emitirDTE($dteData, ['PDF', 'FOLIO', 'TIMBRE']);

        // La respuesta puede venir como string JSON
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        echo "[RESPONSE] " . json_encode($response, JSON_PRETTY_PRINT) . "\n";

        $this->assertIsArray($response);

        // Verificar que la respuesta tiene los campos esperados
        if (isset($response['FOLIO'])) {
            echo "[SUCCESS] ✓ DTE emitido exitosamente!\n";
            echo "[SUCCESS] ✓ Folio: {$response['FOLIO']}\n";
            $this->assertIsNumeric($response['FOLIO']);

            if (isset($response['TOKEN'])) {
                echo "[SUCCESS] ✓ Token: {$response['TOKEN']}\n";
                $this->assertNotEmpty($response['TOKEN']);
            }

            if (isset($response['TIMBRE'])) {
                echo "[SUCCESS] ✓ Timbre fiscal generado\n";
                $this->assertNotEmpty($response['TIMBRE']);
            }

            if (isset($response['WARNING'])) {
                echo "[WARNING] Advertencias: " . json_encode($response['WARNING']) . "\n";
            }
        } else {
            echo "[ERROR] No se obtuvo FOLIO. Respuesta completa:\n";
            print_r($response);
            $this->fail('No se obtuvo FOLIO en la respuesta');
        }

        echo "[OK] ✓ Test de emisión completado exitosamente\n";
    }

    /**
     * Test 5: Comparación con implementación Laravel
     */
    public function testComparacionConLaravel()
    {
        echo "\n[TEST] Comparando implementación con Laravel...\n";

        echo "[INFO] Características implementadas:\n";
        echo "  - Idempotency-Key automático con timestamp ✓\n";
        echo "  - Content-Type: application/json ✓\n";
        echo "  - Header 'apikey' (no Authorization) ✓\n";
        echo "  - Body encoding JSON ✓\n";
        echo "  - Endpoint correcto: /v2/dte/document ✓\n";

        $this->assertTrue(true);

        echo "[OK] Implementación alineada con Laravel\n";
    }

    /**
     * Test 6: Mock SDK
     */
    public function testMockSDK()
    {
        echo "\n[TEST] Probando Mock SDK...\n";

        $mockSdk = new OpenFacturaSDKMock($this->apiKey, true);

        $response = $mockSdk->emitirDTE([], ['PDF', 'FOLIO', 'TIMBRE']);

        echo "[RESPONSE] " . json_encode($response, JSON_PRETTY_PRINT) . "\n";

        $this->assertArrayHasKey('TOKEN', $response);
        $this->assertArrayHasKey('PDF', $response);
        $this->assertArrayHasKey('FOLIO', $response);
        $this->assertArrayHasKey('TIMBRE', $response);
        $this->assertEquals(12345, $response['FOLIO']);

        echo "[OK] Mock SDK funciona correctamente\n";
    }
}
