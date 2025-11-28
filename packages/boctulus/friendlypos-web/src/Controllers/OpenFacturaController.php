<?php
declare(strict_types=1);

namespace Boctulus\FriendlyposWeb\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;
use Boctulus\Simplerest\Core\Libs\Logger;

/**
 * OpenFacturaController
 *
 * API REST para exponer el SDK de OpenFactura (Haulmer - Chile)
 * Este controlador actúa como puente entre NodeJS y el SDK PHP de OpenFactura
 *
 * Cambios principales:
 * - Normaliza sandbox como booleano
 * - Valida existencia de API key antes de inicializar SDK
 * - Métodos success() / error() devuelven la respuesta (retrocompatibilidad con clientes que esperan JSON)
 * - Manejo seguro de excepciones (no exponer trace en producción)
 * - Validaciones básicas en endpoints (folio, fecha, year/month)
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.1.0
 */
class OpenFacturaController extends Controller
{
    private ?string $apiKey = null;
    private bool $sandbox = true;
    private $sdk = null;

    public function __construct()
    {
        parent::__construct();

        // Leer sandbox como booleano (acepta 'true','false', 1, 0, true, false)
        $this->sandbox = filter_var(env('OPENFACTURA_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);

        // Inicializar apiKey
        $this->loadApiKey();
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Cambiar dinámicamente el modo sandbox/producción
     *
     * @param bool $value
     * @return void
     */
    public function useSandbox(bool $value = true): void
    {
        $this->sandbox = (bool)$value;
        $this->loadApiKey();
    }

    /**
     * Carga la API key desde .env según el modo sandbox/production
     */
    private function loadApiKey(): void
    {
        $key = $this->sandbox
            ? env('OPENFACTURA_API_KEY_DEV')
            : env('OPENFACTURA_API_KEY_PROD');

        // Normalizar empty string -> null
        $this->apiKey = ($key === '' || $key === null) ? null : (string)$key;
    }

    /**
     * Inicializa el SDK con los valores actuales (env o sobrescritos)
     *
     * @param string|null $apiKey
     * @param bool|null $sandbox
     * @return mixed SDK instance (tipo depende del SDK)
     * @throws \InvalidArgumentException
     */
    private function initializeSDK(?string $apiKey = null, ?bool $sandbox = null)
    {
        // Si el SDK ya está establecido (por ejemplo, en tests), no reemplazarlo
        if ($this->sdk !== null) {
            return $this->sdk;
        }

        $effectiveApiKey = $apiKey ?? $this->apiKey;
        $effectiveSandbox = $sandbox ?? $this->sandbox;

        if (empty($effectiveApiKey)) {
            throw new \InvalidArgumentException('API key de OpenFactura no configurada (ni en .env ni override).');
        }

        // Factory::make($apiKey, $sandbox, $debug?)
        $sdk = OpenFacturaSDKFactory::make((string)$effectiveApiKey, (bool)$effectiveSandbox, false);

        // Habilitar caché en sandbox para desarrollo si el SDK lo soporta
        if ($effectiveSandbox && method_exists($sdk, 'setCache')) {
            try {
                $sdk->setCache(3600); // 1 hora
            } catch (\Throwable $t) {
                // No fatal; si falla, continuar sin cache
            }
        }

        // Almacenar en propiedad para permitir testing
        $this->sdk = $sdk;

        return $sdk;
    }

    /**
     * Extrae apiKey y sandbox de los headers o body de la request
     *
     * Devuelve ['api_key' => string|null, 'sandbox' => bool|null]
     */
    private function getOverrideParams(): array
    {
        $headers = request()->getHeaders() ?? [];
        $body = request()->getBody(true); // true = decode JSON

        // Normalize header keys case-insensitive
        $normalizedHeaders = [];
        foreach ($headers as $k => $v) {
            // Skip numeric keys (indexed arrays)
            if (is_string($k)) {
                $normalizedHeaders[strtolower($k)] = $v;
            }
        }

        $apiKey = null;
        $sandbox = null;

        // Header priority (case-insensitive)
        if (isset($normalizedHeaders['x-openfactura-api-key'])) {
            $h = $normalizedHeaders['x-openfactura-api-key'];
            $apiKey = is_array($h) ? ($h[0] ?? null) : $h;
        }

        if (isset($normalizedHeaders['x-openfactura-sandbox'])) {
            $h = $normalizedHeaders['x-openfactura-sandbox'];
            $sandbox = is_array($h) ? ($h[0] ?? null) : $h;
        }

        // If not in headers, try body (if provided and is array)
        if ($apiKey === null && is_array($body)) {
            $apiKey = $body['api_key'] ?? $body['apiKey'] ?? null;
        }
        if ($sandbox === null && is_array($body)) {
            $sandbox = $body['sandbox'] ?? null;
        }

        // Convertir sandbox a booleano si está presente
        if ($sandbox !== null) {
            $sandbox = filter_var($sandbox, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            // FILTER_NULL_ON_FAILURE puede retornar null si no se reconoce; en ese caso, dejar como null
        }

        return [
            'api_key' => $apiKey !== null ? (string)$apiKey : null,
            'sandbox' => $sandbox === null ? null : (bool)$sandbox
        ];
    }

    /**
     * POST /api/openfactura/dte/emit
     *
     * Emite un DTE (Documento Tributario Electrónico)
     */
    public function emitDTE()
    {
        try {
            $data = request()->getBody(true); // true = decode JSON

            // Validaciones básicas ANTES de inicializar SDK
            if (!is_array($data) || !isset($data['dteData'])) {
                return $this->error('Campo "dteData" es requerido', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $dteData = $data['dteData'];
            $responseOptions = $data['responseOptions'] ?? ['PDF', 'FOLIO', 'TIMBRE'];
            $sendEmail = $data['sendEmail'] ?? null;
            $idempotencyKey = $data['idempotencyKey'] ?? ('dte_' . uniqid('', true) . '_' . time());

            // Emitir DTE
            $response = $sdk->emitirDTE(
                $dteData,
                $responseOptions,
                null,
                $sendEmail,
                $idempotencyKey
            );

            return $this->success($response);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            // En producción no devolver trace; usar APP_DEBUG para decidir
            $debug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
            if ($debug) {
                return $this->error($e->getMessage(), 500, [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Log internal en vez de revelar información
            if (function_exists('log_error')) {
                log_error('emitDTE error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return $this->error('Error al emitir DTE', 500);
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
            // Validar ANTES de inicializar SDK
            if (empty($token)) {
                return $this->error('Token es requerido', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $status = $sdk->getDTEStatus($token);

            return $this->success($status);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando estado DTE', 500);
        }
    }

    /**
     * POST /api/openfactura/dte/anular-guia
     *
     * Anula una Guía de Despacho (DTE tipo 52)
     */
    public function anularGuiaDespacho()
    {
        try {
            $data = request()->getBody(true);

            // Validaciones ANTES de inicializar SDK
            if (!is_array($data) || !isset($data['folio']) || !isset($data['fecha'])) {
                return $this->error('Campos "folio" y "fecha" son requeridos', 400);
            }

            $folio = $data['folio'];
            $fecha = $data['fecha'];

            // Validar folio numeric
            if (filter_var($folio, FILTER_VALIDATE_INT) === false) {
                return $this->error('Campo "folio" debe ser entero', 400);
            }

            // Validar fecha formato Y-m-d o ISO8601 básico
            $dt = \DateTime::createFromFormat('Y-m-d', (string)$fecha);
            if ($dt === false) {
                // intentar parse general ISO8601
                try {
                    $dt = new \DateTime((string)$fecha);
                } catch (\Exception $ex) {
                    return $this->error('Campo "fecha" debe ser fecha válida (ej: YYYY-MM-DD)', 400);
                }
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $response = $sdk->anularGuiaDespacho((int)$folio, $dt->format('Y-m-d'));

            return $this->success($response);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error al anular guía de despacho', 500);
        }
    }

    /**
     * POST /api/openfactura/dte/anular
     *
     * Anula un DTE mediante una Nota de Crédito (DTE tipo 61)
     */
    public function anularDTE()
    {
        try {
            $data = request()->getBody(true);

            // Validaciones ANTES de inicializar SDK
            if (!is_array($data) || !isset($data['dteData'])) {
                return $this->error('Campo "dteData" es requerido', 400);
            }

            // Validar que sea tipo 61 (Nota de Crédito)
            $tipoDTE = $data['dteData']['Encabezado']['IdDoc']['TipoDTE'] ?? null;
            if ($tipoDTE === null) {
                return $this->error('No se encontró Encabezado->IdDoc->TipoDTE en dteData', 400);
            }

            if ((int)$tipoDTE !== 61) {
                return $this->error('Para anular debe usar TipoDTE 61 (Nota de Crédito)', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $responseOptions = $data['responseOptions'] ?? ['PDF', 'FOLIO'];
            $idempotencyKey = 'anular_' . uniqid('', true) . '_' . time();

            $response = $sdk->emitirDTE(
                $data['dteData'],
                $responseOptions,
                null,
                null,
                $idempotencyKey
            );

            return $this->success($response);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error al anular DTE', 500);
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
            // Validar ANTES de inicializar SDK
            if (empty($rut)) {
                return $this->error('RUT es requerido', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $taxpayer = $sdk->getTaxpayer($rut);

            return $this->success($taxpayer);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando contribuyente', 500);
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
            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $organization = $sdk->getOrganization();

            return $this->success($organization);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando organización', 500);
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
            // Validaciones ANTES de inicializar SDK
            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            if (filter_var($year, FILTER_VALIDATE_INT) === false || filter_var($month, FILTER_VALIDATE_INT) === false) {
                return $this->error('Año y mes deben ser numéricos', 400);
            }

            $y = (int)$year;
            $m = (int)$month;
            if ($m < 1 || $m > 12) {
                return $this->error('Mes inválido (1-12)', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $registry = $sdk->getSalesRegistry($y, $m);

            return $this->success($registry);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando registro de ventas', 500);
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
            // Validaciones ANTES de inicializar SDK
            if (empty($year) || empty($month)) {
                return $this->error('Año y mes son requeridos', 400);
            }

            if (filter_var($year, FILTER_VALIDATE_INT) === false || filter_var($month, FILTER_VALIDATE_INT) === false) {
                return $this->error('Año y mes deben ser numéricos', 400);
            }

            $y = (int)$year;
            $m = (int)$month;
            if ($m < 1 || $m > 12) {
                return $this->error('Mes inválido (1-12)', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $registry = $sdk->getPurchaseRegistry($y, $m);

            return $this->success($registry);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando registro de compras', 500);
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
            // Validaciones ANTES de inicializar SDK
            if (empty($rut) || empty($type) || empty($folio)) {
                return $this->error('RUT, tipo y folio son requeridos', 400);
            }

            // Validar tipo y folio
            if (filter_var($type, FILTER_VALIDATE_INT) === false || filter_var($folio, FILTER_VALIDATE_INT) === false) {
                return $this->error('Tipo y folio deben ser numéricos', 400);
            }

            $overrideParams = $this->getOverrideParams();
            $sdk = $this->initializeSDK($overrideParams['api_key'], $overrideParams['sandbox']);

            $document = $sdk->getDocumentByRutTypeFolio($rut, (int)$type, (int)$folio);

            return $this->success($document);

        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error('Error consultando documento', 500);
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
                'using_override' => ($overrideParams['api_key'] !== null) || ($overrideParams['sandbox'] !== null)
            ]);

        } catch (\Throwable $e) {
            // No exponer detalles en producción
            if (function_exists('log_error')) {
                log_error('health check failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
            return $this->error('Service unhealthy', 503);
        }
    }

    /**
     * Respuesta exitosa
     *
     * Retrocompatible: devuelve JSON con { success: true, data: ..., timestamp: ... }
     *
     * @param mixed $data
     * @param int $statusCode
     * @return mixed (response object / JSON dependiente del framework)
     */
    private function success($data, int $statusCode = 200)
    {
        response()->status($statusCode);
        response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        // Return null to let WebRouter use response()->get()
        return null;
    }

    /**
     * Respuesta de error
     *
     * Retrocompatible: devuelve JSON con { success: false, error: ..., timestamp: ..., ...extra }
     *
     * @param string $message
     * @param int $statusCode
     * @param array $extra
     * @return mixed
     */
    private function error(string $message, int $statusCode = 400, array $extra = [])
    {
        response()->status($statusCode);

        // En producción evita incluir campos sensibles en $extra
        $debug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);

        $payload = [
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if ($debug && !empty($extra)) {
            // solo si APP_DEBUG=true permitimos extra
            $payload = array_merge($payload, $extra);
        } else {
            // si extra incluye keys no sensibles, podemos anexarlas siempre:
            // permitimos anexar solo keys permitidas no sensibles
            $allowedKeys = ['code', 'details'];
            foreach ($allowedKeys as $k) {
                if (array_key_exists($k, $extra)) {
                    $payload[$k] = $extra[$k];
                }
            }
        }

        response()->json($payload);

        // Return null to let WebRouter use response()->get()
        return null;
    }
}
