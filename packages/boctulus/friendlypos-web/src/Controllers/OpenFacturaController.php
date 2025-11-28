<?php

namespace Boctulus\FriendlyposWeb\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;

/**
 * OpenFacturaController
 *
 * API REST para exponer el SDK de OpenFactura (Haulmer - Chile)
 * Este controlador actúa como puente entre NodeJS y el SDK PHP de OpenFactura
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */
class OpenFacturaController extends Controller
{
    private string $apiKey;
    private bool $sandbox;

    public function __construct()
    {
        parent::__construct();

        // Leer sandbox como booleano
        $this->sandbox = env('OPENFACTURA_SANDBOX', true);

        // Inicializar apiKey
        $this->loadApiKey();
    }

    private function loadApiKey(): void
    {
        $this->apiKey = $this->sandbox
            ? env('OPENFACTURA_API_KEY_DEV')
            : env('OPENFACTURA_API_KEY_PROD');
    }

    /**
     * Cambiar dinámicamente el modo sandbox/producción
     */
    public function useSandbox(bool $value = true): void
    {
        $this->sandbox = $value;
        $this->loadApiKey(); // SIEMPRE recargar key, sea dev o prod
    }

    /**
     * Inicializa el SDK con los valores actuales (env o sobrescritos)
     */
    private function initializeSDK($apiKey = null, $sandbox = null)
    {
        // Usar los valores sobrescritos si se proporcionan, de lo contrario usar los predeterminados
        $effectiveApiKey = $apiKey ?? $this->apiKey;
        $effectiveSandbox = $sandbox ?? $this->sandbox;

        // Inicializar SDK
        $sdk = OpenFacturaSDKFactory::make($effectiveApiKey, $effectiveSandbox, false);

        // Habilitar caché en sandbox para desarrollo
        if ($effectiveSandbox) {
            $sdk->setCache(3600); // 1 hora
        }

        return $sdk;
    }

    /**
     * Extrae apiKey y sandbox de los headers o body de la request
     */
    private function getOverrideParams()
    {
        $headers = request()->getHeaders();
        $body = request()->getBody(true); // true = decode JSON

        // Extraer de headers (prioritario)
        $apiKey = $headers['X-Openfactura-Api-Key'][0] ?? null;
        $sandbox = $headers['X-Openfactura-Sandbox'][0] ?? null;

        // Si no están en headers, intentar desde body
        if ($apiKey === null && is_array($body)) {
            $apiKey = $body['api_key'] ?? null;
        }
        if ($sandbox === null && is_array($body)) {
            $sandbox = $body['sandbox'] ?? null;
        }

        // Convertir sandbox a booleano si está presente
        if ($sandbox !== null) {
            $sandbox = filter_var($sandbox, FILTER_VALIDATE_BOOLEAN);
        }

        return [
            'api_key' => $apiKey,
            'sandbox' => $sandbox
        ];
    }

    /**
     * POST /api/openfactura/dte/emit
     *
     * Emite un DTE (Documento Tributario Electrónico)
     *
     * Body esperado:
     * {
     *   "dteData": { ... },           // Datos del DTE según formato SII
     *   "responseOptions": ["PDF", "FOLIO", "TIMBRE"],
     *   "sendEmail": null,            // Opcional
     *   "idempotencyKey": null        // Opcional (se genera automático si no se envía)
     * }
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     *
     * Opciones en Body (alternativa a headers):
     * - api_key: API Key para sobrescribir la del .env
     * - sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function emitDTE()
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $data = request()->getBody(true); // true = decode JSON

            // Validaciones básicas
            if (!isset($data['dteData'])) {
                return $this->error('Campo "dteData" es requerido', 400);
            }

            $dteData = $data['dteData'];
            $responseOptions = $data['responseOptions'] ?? ['PDF', 'FOLIO', 'TIMBRE'];
            $sendEmail = $data['sendEmail'] ?? null;
            $idempotencyKey = $data['idempotencyKey'] ?? 'dte_' . uniqid() . '_' . time();

            // Emitir DTE
            $response = $sdk->emitirDTE(
                $dteData,
                $responseOptions,
                null,
                $sendEmail,
                $idempotencyKey
            );

            return $this->success($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500, [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * GET /api/openfactura/dte/status/{token}
     *
     * Consulta el estado de un DTE previamente emitido
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getDTEStatus($token = null)
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            if (empty($token)) {
                return $this->error('Token es requerido', 400);
            }

            $status = $sdk->getDTEStatus($token);

            return $this->success($status);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/openfactura/dte/anular-guia
     *
     * Anula una Guía de Despacho (DTE tipo 52)
     *
     * Body esperado:
     * {
     *   "folio": 12345,
     *   "fecha": "2025-01-15"
     * }
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function anularGuiaDespacho()
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $data = request()->getBody(true);

            if (!isset($data['folio']) || !isset($data['fecha'])) {
                return $this->error('Campos "folio" y "fecha" son requeridos', 400);
            }

            $response = $sdk->anularGuiaDespacho($data['folio'], $data['fecha']);

            return $this->success($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/openfactura/dte/anular
     *
     * Anula un DTE mediante una Nota de Crédito (DTE tipo 61)
     *
     * Body esperado:
     * {
     *   "dteData": { ... },  // Datos de la Nota de Crédito
     *   "responseOptions": ["PDF", "FOLIO"]
     * }
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function anularDTE()
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $data = request()->getBody(true);

            if (!isset($data['dteData'])) {
                return $this->error('Campo "dteData" es requerido', 400);
            }

            // Validar que sea tipo 61 (Nota de Crédito)
            $tipoDTE = $data['dteData']['Encabezado']['IdDoc']['TipoDTE'] ?? null;
            if ($tipoDTE != 61) {
                return $this->error('Para anular debe usar TipoDTE 61 (Nota de Crédito)', 400);
            }

            $responseOptions = $data['responseOptions'] ?? ['PDF', 'FOLIO'];
            $idempotencyKey = 'anular_' . uniqid() . '_' . time();

            $response = $sdk->emitirDTE(
                $data['dteData'],
                $responseOptions,
                null,
                null,
                $idempotencyKey
            );

            return $this->success($response);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/taxpayer/{rut}
     *
     * Consulta datos de un contribuyente por RUT
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getTaxpayer($rut = null)
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            if (empty($rut)) {
                return $this->error('RUT es requerido', 400);
            }

            $taxpayer = $sdk->getTaxpayer($rut);

            return $this->success($taxpayer);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/organization
     *
     * Obtiene información de la organización configurada
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getOrganization()
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $organization = $sdk->getOrganization();

            return $this->success($organization);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/sales-registry/{year}/{month}
     *
     * Obtiene el registro de ventas de un período
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getSalesRegistry($year = null, $month = null)
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            $registry = $sdk->getSalesRegistry($year, $month);

            return $this->success($registry);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/purchase-registry/{year}/{month}
     *
     * Obtiene el registro de compras de un período
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getPurchaseRegistry($year = null, $month = null)
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            $registry = $sdk->getPurchaseRegistry($year, $month);

            return $this->success($registry);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/document/{rut}/{type}/{folio}
     *
     * Obtiene un documento específico por RUT, tipo y folio
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function getDocument($rut = null, $type = null, $folio = null)
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            if (empty($rut) || empty($type) || empty($folio)) {
                return $this->error('RUT, tipo y folio son requeridos', 400);
            }

            $document = $sdk->getDocumentByRutTypeFolio($rut, $type, $folio);

            return $this->success($document);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/health
     *
     * Health check del servicio
     *
     * Headers Opcionales:
     * - X-Openfactura-Api-Key: API Key para sobrescribir la del .env
     * - X-Openfactura-Sandbox: true/false para sobrescribir el modo sandbox del .env
     */
    public function health()
    {
        try {
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $apiStatus = $sdk->checkApiStatus();

            // Determine which sandbox value to display (the one used for this request)
            $effectiveSandbox = $overrideParams['sandbox'] ?? $this->sandbox;

            return $this->success([
                'service' => 'OpenFactura API',
                'status' => 'healthy',
                'sandbox' => $effectiveSandbox,
                'api_status' => $apiStatus,
                'timestamp' => date('Y-m-d H:i:s'),
                'using_override' => $overrideParams['api_key'] !== null || $overrideParams['sandbox'] !== null
            ]);

        } catch (\Exception $e) {
            return $this->error('Service unhealthy', 503, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Respuesta exitosa
     */
    private function success($data, $statusCode = 200)
    {
        response()->status($statusCode);
        response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Respuesta de error
     */
    private function error($message, $statusCode = 400, $extra = [])
    {
        response()->status($statusCode);
        response()->json([
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
            ...$extra
        ]);
    }
}
