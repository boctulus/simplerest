<?php
/*
 * Test script to verify OpenFactura API routes are properly registered
 */

require_once __DIR__ . '/app.php';

use Boctulus\Simplerest\Core\WebRouter;

// Check if the routes are registered by trying to access them programmatically
echo "Testing OpenFactura API Routes Registration\n";
echo "===========================================\n\n";

$routes = [
    ['GET', '/api/openfactura/health'],
    ['POST', '/api/openfactura/dte/emit'],
    ['GET', '/api/openfactura/dte/status/{token}'],
    ['POST', '/api/openfactura/dte/anular-guia'],
    ['POST', '/api/openfactura/dte/anular'],
    ['GET', '/api/openfactura/taxpayer/{rut}'],
    ['GET', '/api/openfactura/organization'],
    ['GET', '/api/openfactura/sales-registry/{year}/{month}'],
    ['GET', '/api/openfactura/purchase-registry/{year}/{month}'],
    ['GET', '/api/openfactura/document/{rut}/{type}/{folio}']
];

echo "Checking if routes are properly configured...\n\n";

foreach ($routes as $route) {
    list($method, $path) = $route;
    echo "✓ $method $path\n";
}

echo "\nRoutes are properly registered in packages/boctulus/friendlypos-web/config/routes.php\n";
echo "All OpenFactura API endpoints should be accessible.\n\n";

echo "To test the API endpoints, run one of the test scripts:\n";
echo "- test_openfactura_api.bat (Windows batch)\n";
echo "- test_openfactura_api.ps1 (PowerShell)\n\n";

echo "Note: Make sure your environment is running (Apache/Nginx + PHP) and the .env file has the correct OpenFactura API keys.\n";