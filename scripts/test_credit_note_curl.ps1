# Test: Emisión de Nota de Crédito (DTE tipo 61) usando PowerShell
#
# Este script prueba la emisión de una Nota de Crédito directamente
# contra el endpoint REST del controlador OpenFacturaController
#
# Ejecutar con: powershell -File tests\test_credit_note_curl.ps1
#
# @author Pablo Bozzolo (boctulus)

Write-Host ""
Write-Host "========================================"
Write-Host "TEST: Emisión de Nota de Crédito con PowerShell"
Write-Host "========================================"
Write-Host ""

# Leer .env si existe
$envFile = Join-Path (Split-Path $PSScriptRoot -Parent) ".env"
if (Test-Path $envFile) {
    Get-Content $envFile | ForEach-Object {
        if ($_ -match '^\s*([^#][^=]+)=(.*)$') {
            $name = $matches[1].Trim()
            $value = $matches[2].Trim()
            Set-Item -Path "env:$name" -Value $value
        }
    }
}

# Determinar modo sandbox
$SANDBOX_STR = if ($env:OPENFACTURA_SANDBOX) { $env:OPENFACTURA_SANDBOX } else { "true" }
$SANDBOX = $SANDBOX_STR -eq "true"

# Seleccionar API key según modo
if ($SANDBOX) {
    $API_KEY = $env:OPENFACTURA_API_KEY_DEV
    $MODE = "SANDBOX (Desarrollo)"
} else {
    $API_KEY = $env:OPENFACTURA_API_KEY_PROD
    $MODE = "PRODUCCIÓN"
}

$BASE_URL = if ($env:APP_URL) { $env:APP_URL } else { "http://simplerest.lan" }
$ENDPOINT = "$BASE_URL/api/v1/openfactura/dte/emit"

Write-Host "Configuración:"
Write-Host "  - Modo: $MODE"
Write-Host "  - Base URL: $BASE_URL"
Write-Host "  - API Key: $($API_KEY.Substring(0, 10))..."
Write-Host "  - Endpoint: $ENDPOINT"
Write-Host "  - OPENFACTURA_SANDBOX: $SANDBOX_STR"
Write-Host ""

# Construir el payload JSON
# IMPORTANTE: Este payload usa la estructura CORRECTA para Notas de Crédito
$payload = @{
    dteData = @{
        Encabezado = @{
            IdDoc = @{
                TipoDTE = 61
                Folio = 0
                FchEmis = "2026-01-20"
                IndNoRebaja = 1
            }
            Emisor = @{
                RUTEmisor = "76795561-8"
                RznSoc = "HAULMER SPA"
                GiroEmis = "COMERCIO"
                Acteco = 479110
                DirOrigen = "Pasaje Los Pajaritos 8357"
                CmnaOrigen = "Santiago"
            }
            Receptor = @{
                RUTRecep = "76795561-8"
                RznSocRecep = "HAULMER SPA"
                GiroRecep = "COMERCIO"
                DirRecep = "Pasaje Los Pajaritos 8357"
                CmnaRecep = "Santiago"
            }
            Totales = @{
                MntNeto = 84
                TasaIVA = 19
                IVA = 16
                MntTotal = 100
            }
        }
        Detalle = @(
            @{
                NroLinDet = 1
                NmbItem = "AAAA"
                QtyItem = 1
                PrcItem = 84
                MontoItem = 84
            }
        )
        Referencia = @(
            @{
                NroLinRef = 1
                TpoDocRef = 39
                FolioRef = 631563
                FchRef = "2026-01-17"
                CodRef = 1
                RazonRef = "Alguna referencia"
                IndGlobal = 1
            }
        )
    }
    responseOptions = @("PDF", "FOLIO", "TIMBRE")
} | ConvertTo-Json -Depth 10

Write-Host "Enviando petición..."
Write-Host ""

try {
    # Hacer la petición con Invoke-RestMethod
    $headers = @{
        "Content-Type" = "application/json"
        "X-Openfactura-Api-Key" = $API_KEY
        "X-Openfactura-Sandbox" = $SANDBOX_STR
    }

    $response = Invoke-RestMethod -Uri $ENDPOINT -Method Post -Headers $headers -Body $payload

    Write-Host "Respuesta:"
    Write-Host ($response | ConvertTo-Json -Depth 10)

    if ($response.success) {
        Write-Host ""
        Write-Host "✓ ÉXITO:" -ForegroundColor Green

        if ($response.data.FOLIO) {
            Write-Host "  - Folio: $($response.data.FOLIO)"
        }

        if ($response.data.TOKEN) {
            Write-Host "  - Token: $($response.data.TOKEN)"
        }

        if ($response.data.PDF) {
            Write-Host "  - PDF: Generado (base64)"
        }

        if ($response.data.TIMBRE) {
            Write-Host "  - Timbre: Generado (base64)"
        }
    } else {
        Write-Host ""
        Write-Host "✗ ERROR:" -ForegroundColor Red
        Write-Host "  - Mensaje: $($response.error)"
    }

} catch {
    Write-Host ""
    Write-Host "✗ ERROR EN LA PETICIÓN:" -ForegroundColor Red
    Write-Host "  - Mensaje: $($_.Exception.Message)"

    if ($_.ErrorDetails.Message) {
        $errorResponse = $_.ErrorDetails.Message | ConvertFrom-Json
        Write-Host ""
        Write-Host "Detalles del error:"
        Write-Host ($errorResponse | ConvertTo-Json -Depth 10)
    }
}

Write-Host ""
Write-Host "========================================"
Write-Host "Test finalizado"
Write-Host "========================================"
Write-Host ""
