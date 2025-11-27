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
    private $sdk;
    private $apiKey;
    private $sandbox;

    public function __construct()
    {
        parent::__construct();

        // Cargar configuración desde .env
        $this->sandbox = env('OPENFACTURA_SANDBOX', 'true') === 'true';

        $this->apiKey = $this->sandbox
            ? env('OPENFACTURA_API_KEY_DEV')
            : env('OPENFACTURA_API_KEY_PROD');

        // Inicializar SDK
        $this->sdk = OpenFacturaSDKFactory::make($this->apiKey, $this->sandbox, false);

        // Habilitar caché en sandbox para desarrollo
        if ($this->sandbox) {
            $this->sdk->setCache(3600); // 1 hora
        }
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
     */
    public function emitDTE()
    {
        try {
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
            $response = $this->sdk->emitirDTE(
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
     */
    public function getDTEStatus($token = null)
    {
        try {
            if (empty($token)) {
                return $this->error('Token es requerido', 400);
            }

            $status = $this->sdk->getDTEStatus($token);

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
     */
    public function anularGuiaDespacho()
    {
        try {
            $data = request()->getBody(true);

            if (!isset($data['folio']) || !isset($data['fecha'])) {
                return $this->error('Campos "folio" y "fecha" son requeridos', 400);
            }

            $response = $this->sdk->anularGuiaDespacho($data['folio'], $data['fecha']);

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
     */
    public function anularDTE()
    {
        try {
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

            $response = $this->sdk->emitirDTE(
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
     */
    public function getTaxpayer($rut = null)
    {
        try {
            if (empty($rut)) {
                return $this->error('RUT es requerido', 400);
            }

            $taxpayer = $this->sdk->getTaxpayer($rut);

            return $this->success($taxpayer);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/organization
     *
     * Obtiene información de la organización configurada
     */
    public function getOrganization()
    {
        try {
            $organization = $this->sdk->getOrganization();

            return $this->success($organization);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/sales-registry/{year}/{month}
     *
     * Obtiene el registro de ventas de un período
     */
    public function getSalesRegistry($year = null, $month = null)
    {
        try {
            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            $registry = $this->sdk->getSalesRegistry($year, $month);

            return $this->success($registry);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/purchase-registry/{year}/{month}
     *
     * Obtiene el registro de compras de un período
     */
    public function getPurchaseRegistry($year = null, $month = null)
    {
        try {
            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            $registry = $this->sdk->getPurchaseRegistry($year, $month);

            return $this->success($registry);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/document/{rut}/{type}/{folio}
     *
     * Obtiene un documento específico por RUT, tipo y folio
     */
    public function getDocument($rut = null, $type = null, $folio = null)
    {
        try {
            if (empty($rut) || empty($type) || empty($folio)) {
                return $this->error('RUT, tipo y folio son requeridos', 400);
            }

            $document = $this->sdk->getDocumentByRutTypeFolio($rut, $type, $folio);

            return $this->success($document);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/openfactura/health
     *
     * Health check del servicio
     */
    public function health()
    {
        try {
            $apiStatus = $this->sdk->checkApiStatus();

            return $this->success([
                'service' => 'OpenFactura API',
                'status' => 'healthy',
                'sandbox' => $this->sandbox,
                'api_status' => $apiStatus,
                'timestamp' => date('Y-m-d H:i:s')
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
