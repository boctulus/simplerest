@echo off
setlocal enabledelayedexpansion

echo Testing OpenFactura API Endpoints
echo ================================

REM Set base URL
set "BASE_URL=http://simplerest.lan"

echo.
echo 1. Testing Health Check Endpoint
echo -------------------------------
curl -X GET "!BASE_URL!/api/openfactura/health" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 2. Testing Emit DTE Endpoint (will fail without proper data)
echo ------------------------------------------------------------
curl -X POST "!BASE_URL!/api/openfactura/dte/emit" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"dteData\": {}}" ^
  --verbose
echo.
echo.

echo 3. Testing Get DTE Status Endpoint (will fail without token)
echo ------------------------------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/dte/status/invalid_token" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 4. Testing Anular Guia Despacho Endpoint (will fail without proper data)
echo -----------------------------------------------------------------------
curl -X POST "!BASE_URL!/api/openfactura/dte/anular-guia" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"folio\": 12345, \"fecha\": \"2025-01-15\"}" ^
  --verbose
echo.
echo.

echo 5. Testing Anular DTE Endpoint (will fail without proper data)
echo ------------------------------------------------------------
curl -X POST "!BASE_URL!/api/openfactura/dte/anular" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"dteData\": {}}" ^
  --verbose
echo.
echo.

echo 6. Testing Get Taxpayer Endpoint (will fail without RUT)
echo --------------------------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/taxpayer/12345678-9" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 7. Testing Get Organization Endpoint
echo ------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/organization" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 8. Testing Get Sales Registry Endpoint (will fail without year/month)
echo --------------------------------------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/sales-registry/2025/01" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 9. Testing Get Purchase Registry Endpoint (will fail without year/month)
echo -----------------------------------------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/purchase-registry/2025/01" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo 10. Testing Get Document Endpoint (will fail without proper parameters)
echo -----------------------------------------------------------------------
curl -X GET "!BASE_URL!/api/openfactura/document/12345678-9/33/12345" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --verbose
echo.
echo.

echo All tests completed!
pause