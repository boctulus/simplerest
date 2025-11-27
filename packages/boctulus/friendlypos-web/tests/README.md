# OpenFactura Controller Tests

This directory contains unit tests for the OpenFacturaController that integrates with the OpenFactura SDK for electronic invoicing in Chile.

## Test Files

### 1. OpenFacturaControllerTest.php
- Tests basic controller functionality
- Tests all endpoint methods with valid and invalid data
- Tests error handling for missing or incorrect parameters

### 2. OpenFacturaControllerSdkTest.php
- Tests SDK integration with mocked responses
- Tests environment variable handling (sandbox vs production)
- Tests internal success/error response methods

### 3. OpenFacturaControllerErrorTest.php
- Tests error handling when the SDK throws exceptions
- Tests proper error responses for all endpoints
- Tests graceful failure scenarios

## Running the Tests

### Prerequisites
- PHP 7.4 or higher
- Composer dependencies installed
- PHPUnit 10.0 or higher

### Running All Tests
```bash
cd /path/to/simplerest
php vendor/bin/phpunit packages/boctulus/friendlypos-web/tests/
```

### Running Individual Test Files
```bash
# Run basic functionality tests
php vendor/bin/phpunit packages/boctulus/friendlypos-web/tests/OpenFacturaControllerTest.php

# Run SDK integration tests
php vendor/bin/phpunit packages/boctulus/friendlypos-web/tests/OpenFacturaControllerSdkTest.php

# Run error handling tests
php vendor/bin/phpunit packages/boctulus/friendlypos-web/tests/OpenFacturaControllerErrorTest.php
```

### Running with More Verbose Output
```bash
php vendor/bin/phpunit packages/boctulus/friendlypos-web/tests/ --verbose
```

### Using Composer Script (if available)
```bash
composer test
```

## Test Coverage

The tests cover:
- All public methods in OpenFacturaController
- Error handling scenarios
- Successful API responses
- Parameter validation
- Environment configuration (sandbox vs production)
- SDK integration points
- All API endpoints:
  - `/api/openfactura/dte/emit`
  - `/api/openfactura/dte/status/{token}`
  - `/api/openfactura/dte/anular-guia`
  - `/api/openfactura/dte/anular`
  - `/api/openfactura/taxpayer/{rut}`
  - `/api/openfactura/organization`
  - `/api/openfactura/sales-registry/{year}/{month}`
  - `/api/openfactura/purchase-registry/{year}/{month}`
  - `/api/openfactura/document/{rut}/{type}/{folio}`
  - `/api/openfactura/health`

## Environment Variables

The tests mock the following environment variables:
- `OPENFACTURA_SANDBOX`: Determines sandbox vs production mode
- `OPENFACTURA_API_KEY_DEV`: Development API key
- `OPENFACTURA_API_KEY_PROD`: Production API key

## Mocking Strategy

- The SDK is mocked to prevent actual API calls during testing
- Request and Response objects are mocked to control input/output
- All external dependencies are mocked to ensure isolated tests

## Expected Test Results

All tests should pass with no errors or failures. If any tests fail:
1. Check that all dependencies are installed
2. Verify the framework is properly initialized
3. Ensure environment variables are set correctly
4. Review any recent changes to the OpenFacturaController or SDK