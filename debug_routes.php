<?php
/*
 * Debug script to check if OpenFactura routes are properly loaded
 */

// Load the framework
require_once __DIR__ . '/app.php';

use Boctulus\Simplerest\Core\WebRouter;

// Check if routes are registered
echo "Checking if OpenFactura routes are properly registered...\n";

// Get the registered routes (this may require checking the framework's implementation)
// For now, at least confirm the packages are loaded

echo "OpenFacturaController class exists: " . (class_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController') ? 'YES' : 'NO') . "\n";
echo "Method emitDTE exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'emitDTE') ? 'YES' : 'NO') . "\n";
echo "Method getDTEStatus exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getDTEStatus') ? 'YES' : 'NO') . "\n";
echo "Method anularGuiaDespacho exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'anularGuiaDespacho') ? 'YES' : 'NO') . "\n";
echo "Method anularDTE exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'anularDTE') ? 'YES' : 'NO') . "\n";
echo "Method getTaxpayer exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getTaxpayer') ? 'YES' : 'NO') . "\n";
echo "Method getOrganization exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getOrganization') ? 'YES' : 'NO') . "\n";
echo "Method getSalesRegistry exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getSalesRegistry') ? 'YES' : 'NO') . "\n";
echo "Method getPurchaseRegistry exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getPurchaseRegistry') ? 'YES' : 'NO') . "\n";
echo "Method getDocument exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'getDocument') ? 'YES' : 'NO') . "\n";
echo "Method health exists: " . (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'health') ? 'YES' : 'NO') . "\n";

echo "\nVerifying if the package service provider is loaded:\n";
echo "FriendlyposWeb ServiceProvider class exists: " . (class_exists('Boctulus\FriendlyposWeb\ServiceProvider') ? 'YES' : 'NO') . "\n";

echo "\nVerifying if OpenFactura SDK components exist:\n";
echo "OpenFacturaSDKFactory class exists: " . (class_exists('Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory') ? 'YES' : 'NO') . "\n";
echo "OpenFacturaSDKMock class exists: " . (class_exists('Boctulus\OpenfacturaSdk\Mocks\OpenFacturaSDKMock') ? 'YES' : 'NO') . "\n";

echo "\nDone.\n";