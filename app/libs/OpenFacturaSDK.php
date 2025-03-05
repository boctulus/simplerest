<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

/*
    SDK para OpenFactura

    @version 1.0.0

    @author Pablo Bozzolo

    API KEY de desarrollo "928e15a2d14d4a6292345f04960f4bd3"

    Metodos

    - emitirDTE: Emisión de DTEs con soporte para opciones de respuesta (response), campos personalizados (custom), envío de email (sendEmail) y clave de idempotencia (Idempotency-Key).
    - getDTEStatus: Consulta del estado de un DTE por token.
    - anularDTE52: Anulación de guías de despacho electrónicas (DTE tipo 52).
    - getCompanyInfo: Obtención de información de la empresa.
    - Métodos adicionales como getTaxpayer, getOrganization, getOrganizationDocuments,...

    Manejo de Idempotencia

    El método emitirDTE permite configurar una clave de idempotencia mediante Idempotency-Key, lo cual es una práctica recomendada por la documentación para evitar duplicados en reintentos. 

    if ($idempotencyKey) {
        $this->apiClient->setHeader('Idempotency-Key', $idempotencyKey);

    La cabecera se elimina después de la solicitud, evitando interferencias en futuras peticiones.    
}
*/
class OpenFacturaSDK 
{
    private $apiClient;
    private $apiKey;
    private $base_url;

    /**
     * Constructor de la clase.
     *
     * @param string $apiKey Clave API para autenticación.
     * @param bool $sandbox Indica si se usa el entorno de desarrollo (true) o producción (false).
     */
    public function __construct($apiKey, $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->base_url = $sandbox ? 'https://dev-api.haulmer.com' : 'https://api.haulmer.com';

        $this->apiClient = (new ApiClient())
            ->setHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ]);
    }

    /**
     * Configura el tiempo de expiración de la caché para las peticiones.
     *
     * @param int $expiration_time Tiempo en segundos.
     * @return self
     */
    public function setCache($expiration_time) {
        $this->apiClient->cache($expiration_time);
        return $this;
    }

    /**
     * Emite un Documento Tributario Electrónico (DTE).
     *
     * @param array $dteData Datos del DTE según formato SII.
     * @param array $responseOptions Tipos de respuesta deseados (e.g., ['PDF', 'FOLIO']).
     * @param array|null $custom Campos personalizados (informationNote, paymentNote).
     * @param array|null $sendEmail Opciones de envío de email (to, CC, BCC).
     * @param string|null $idempotencyKey Clave de idempotencia para evitar duplicados.
     * @return mixed Respuesta de la API.
     */
    public function emitirDTE($dteData, $responseOptions = [], $custom = null, $sendEmail = null, $idempotencyKey = null) {
        $body = array_filter([
            'dte' => $dteData,
            'response' => $responseOptions,
            'custom' => $custom,
            'sendEmail' => $sendEmail
        ], function ($value) {
            return $value !== null && $value !== [];
        });

        if ($idempotencyKey) {
            $this->apiClient->setHeader('Idempotency-Key', $idempotencyKey);
        }

        $response = $this->apiClient
            ->setBody($body, true)
            ->request($this->base_url . '/v2/dte/document', 'POST')
            ->data();

        if ($idempotencyKey) {
            $this->apiClient->unsetHeader('Idempotency-Key');
        }

        return $response;
    }

    /**
     * Consulta el estado de un DTE mediante su token.
     *
     * @param string $token Token del DTE.
     * @return mixed Respuesta de la API.
     */
    public function getDTEStatus($token) {
        return $this->apiClient
            ->request($this->base_url . '/v2/dte/document/' . $token . '/status', 'GET')
            ->data();
    }   

    /**
     * Anula una Guía de Despacho Electrónica (DTE tipo 52).
     *
     * @param int $folio Folio del documento.
     * @param string $fecha Fecha de emisión (formato AAAA-MM-DD).
     * @return mixed Respuesta de la API.
     */
    public function anularDTE52($folio, $fecha) {
        $body = [
            'Dte' => 52,
            'Folio' => $folio,
            'Fecha' => $fecha
        ];

        return $this->apiClient
            ->setBody($body, true)
            ->request($this->base_url . '/v2/dte/anularDTE52', 'POST')
            ->data();
    }

    /**
     * Obtiene información de la empresa asociada a la API Key.
     *
     * @return mixed Respuesta de la API.
     */
    public function getCompanyInfo() {
        return $this->apiClient
            ->request($this->base_url . '/v2/dte/company', 'GET')
            ->data();
    }

    /**
     * Get taxpayer details by RUT.
     *
     * @param string $rut The RUT of the taxpayer.
     * @return mixed The API response data.
     */
    public function getTaxpayer($rut) {
        return $this->apiClient->get($this->base_url . '/v2/dte/taxpayer/' . urlencode($rut))->data();
    }

    /**
     * Get organization details.
     *
     * @return mixed The API response data.
     */
    public function getOrganization() {
        return $this->apiClient->get($this->base_url . '/v2/dte/organization')->data();
    }

    /**
     * Get organization documents.
     *
     * @param array $queryParams Optional query parameters for filtering.
     * @return mixed The API response data.
     */
    public function getOrganizationDocuments($queryParams = []) {
        $url = $this->base_url . '/v2/dte/organization/document';
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get purchase registry for a specific year and month.
     *
     * @param string $year The year (e.g., "2023").
     * @param string $month The month (e.g., "01").
     * @param array $queryParams Optional query parameters.
     * @return mixed The API response data.
     */
    public function getPurchaseRegistry($year, $month, $queryParams = []) {
        $url = $this->base_url . '/v2/dte/registry/purchase/' . urlencode($year) . '/' . urlencode($month);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get sales registry for a specific year and month.
     *
     * @param string $year The year (e.g., "2023").
     * @param string $month The month (e.g., "01").
     * @param array $queryParams Optional query parameters.
     * @return mixed The API response data.
     */
    public function getSalesRegistry($year, $month, $queryParams = []) {
        $url = $this->base_url . '/v2/dte/registry/sales/' . urlencode($year) . '/' . urlencode($month);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get a document by RUT, type, and folio.
     *
     * @param string $rut The RUT identifier.
     * @param string $type The document type.
     * @param string $folio The document folio number.
     * @return mixed The API response data.
     */
    public function getDocumentByRutTypeFolio($rut, $type, $folio) {
        $url = $this->base_url . '/v2/dte/document/' . urlencode($rut) . '/' . urlencode($type) . '/' . urlencode($folio);
        return $this->apiClient->get($url)->data();
    }

    /**
     * Get a document by token and value.
     *
     * @param string $token The token identifier.
     * @param string $value The value associated with the token.
     * @return mixed The API response data.
     */
    public function getDocumentByTokenValue($token, $value) {
        $url = $this->base_url . '/v2/dte/document/' . urlencode($token) . '/' . urlencode($value);
        return $this->apiClient->get($url)->data();
    }

    /**
     * Handle issued documents (exact purpose depends on API specifics).
     *
     * @param array $data Data for the issued document action.
     * @return mixed The API response data.
     */
    public function documentIssued($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/issued')->data();
    }

    /**
     * Handle received documents.
     *
     * @param array $data Data for the received document action.
     * @return mixed The API response data.
     */
    public function documentReceived($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/received')->data();
    }

    /**
     * Acknowledge received documents.
     *
     * @param array $data Data for the accusation of received documents.
     * @return mixed The API response data.
     */
    public function documentReceivedAccuse($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/document/received/accuse')->data();
    }

    /**
     * Generate a self-service link for DTE (assumed endpoint).
     *
     * @param array $data Data required to generate the self-service link.
     * @return mixed The API response data.
     */
    public function emitirEnlaceAutoservicio($data) {
        return $this->apiClient->setBody($data)->post($this->base_url . '/v2/dte/selfservice')->data();
    }
}   

