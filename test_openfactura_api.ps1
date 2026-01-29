Write-Host "Testing OpenFactura API Endpoints" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

# Set base URL
$BASE_URL = "http://simplerest.lan"

Write-Host "`n1. Testing Health Check Endpoint" -ForegroundColor Yellow
Write-Host "-------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/health" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "2. Testing Emit DTE Endpoint (will fail without proper data)" -ForegroundColor Yellow
Write-Host "-------------------------------------------------------------"
try {
    $body = @{dteData = @{}} | ConvertTo-Json
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/dte/emit" -Method Post -ContentType "application/json" -Body $body
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "3. Testing Get DTE Status Endpoint (will fail without token)" -ForegroundColor Yellow
Write-Host "-------------------------------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/dte/status/invalid_token" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "4. Testing Anular Guia Despacho Endpoint (will fail without proper data)" -ForegroundColor Yellow
Write-Host "------------------------------------------------------------------------"
try {
    $body = @{folio = 12345; fecha = "2025-01-15"} | ConvertTo-Json
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/dte/anular-guia" -Method Post -ContentType "application/json" -Body $body
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "5. Testing Anular DTE Endpoint (will fail without proper data)" -ForegroundColor Yellow
Write-Host "-------------------------------------------------------------"
try {
    $body = @{dteData = @{}} | ConvertTo-Json
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/dte/anular" -Method Post -ContentType "application/json" -Body $body
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "6. Testing Get Taxpayer Endpoint (will fail without RUT)" -ForegroundColor Yellow
Write-Host "--------------------------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/taxpayer/12345678-9" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "7. Testing Get Organization Endpoint" -ForegroundColor Yellow
Write-Host "------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/organization" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "8. Testing Get Sales Registry Endpoint (will fail without year/month)" -ForegroundColor Yellow
Write-Host "-------------------------------------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/sales-registry/2025/01" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "9. Testing Get Purchase Registry Endpoint (will fail without year/month)" -ForegroundColor Yellow
Write-Host "-----------------------------------------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/purchase-registry/2025/01" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "10. Testing Get Document Endpoint (will fail without proper parameters)" -ForegroundColor Yellow
Write-Host "-----------------------------------------------------------------------"
try {
    $response = Invoke-RestMethod -Uri "$BASE_URL/api/v1/openfactura/document/12345678-9/33/12345" -Method Get -ContentType "application/json"
    $response | ConvertTo-Json -Depth 10
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`nAll tests completed!" -ForegroundColor Green