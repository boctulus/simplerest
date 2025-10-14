<?php

namespace Boctulus\Simplerest\Interfaces;

interface IOpenFactura {
    public function __construct($apiKey, $sandbox = false);
    public function setCache($expiration_time);
    public function emitirDTE($dteData, $responseOptions = array (
), $custom = NULL, $sendEmail = NULL, $idempotencyKey = NULL);
    public function getDTEStatus($token);
    public function anularGuiaDespacho($folio, $fecha);
    public function anularDTE52($folio, $fecha);
    public function getCompanyInfo();
    public function getTaxpayer($rut);
    public function listTaxpayers($queryParams = array (
));
    public function getOrganization();
    public function getOrganizationDocuments($queryParams = array (
));
    public function getPurchaseRegistry($year, $month, $queryParams = array (
));
    public function getSalesRegistry($year, $month, $queryParams = array (
));
    public function getDocumentByRutTypeFolio($rut, $type, $folio);
    public function getDocumentByTokenValue($token, $value);
    public function documentIssued($data);
    public function documentReceived($data);
    public function documentReceivedAccuse($data);
    public function emitirEnlaceAutoservicio($data);
    public function getDocumentDetails($token);
    public function sendDocumentEmail($token, $emailData);
    public function checkApiStatus();
}