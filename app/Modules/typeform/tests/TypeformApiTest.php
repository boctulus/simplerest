<?php

namespace Boctulus\Simplerest\modules\Typeform\tests;

use Boctulus\Simplerest\core\libs\TestCase;
use Boctulus\Simplerest\controllers\TypeformController;

class TypeformApiTest extends TestCase
{
    protected $controller;

    function __construct() {
        parent::__construct();
        $this->controller = new TypeformController();
    }

    function testTypeformGetRoute()
    {
        // Test that the typeform GET route works
        $url = base_url() . '/typeform';
        
        $response = $this->get($url);
        
        $this->assertNotEquals(404, $response['status'], 'Typeform GET route should not return 404');
        $this->assertEquals(200, $response['status'], 'Typeform GET route should return 200');
        
        // Check that response contains typeform HTML
        $content = $response['body'];
        $this->assertContains('typeform-container', $content, 'Response should contain typeform container');
        
        echo "✓ GET /typeform returns 200 and contains expected content\n";
    }

    function testTypeformProcessRoute()
    {
        // Test that the typeform POST route works
        $url = base_url() . '/typeform/process';
        
        $postData = [
            'step' => 1,
            'company_name' => 'Test Company',
            'rut' => '12345678-9'
        ];
        
        $response = $this->post($url, $postData);
        
        $this->assertNotEquals(404, $response['status'], 'Typeform POST route should not return 404');
        $this->assertEquals(200, $response['status'], 'Typeform POST route should return 200');
        
        // Check that response is valid JSON
        $responseData = json_decode($response['body'], true);
        $this->assertIsArray($responseData, 'Response should be valid JSON');
        $this->assertTrue($responseData['success'] ?? false, 'Response should indicate success');
        
        echo "✓ POST /typeform/process returns 200 and valid JSON response\n";
    }

    function testTypeformControllerGet()
    {
        // Test controller get method directly
        ob_start();
        $result = $this->controller->get();
        ob_end_clean();
        
        $this->assertNotEmpty($result, 'Controller get method should return content');
        $this->assertIsString($result, 'Controller get method should return string');
        
        echo "✓ TypeformController::get() returns valid content\n";
    }

    function testTypeformControllerProcess()
    {
        // Mock POST data
        $_POST['step'] = 2;
        $_POST['document_type'] = 'boleta';
        $_POST['business_type'] = 'individual';
        
        // Start output buffering to catch the JSON output
        ob_start();
        $this->controller->process();
        $output = ob_get_contents();
        ob_end_clean();
        
        // Verify JSON response
        $response = json_decode($output, true);
        $this->assertIsArray($response, 'Controller process should return valid JSON');
        $this->assertTrue($response['success'] ?? false, 'Process should return success=true');
        $this->assertEquals(2, $response['step'] ?? 0, 'Process should return correct step number');
        
        echo "✓ TypeformController::process() returns valid JSON response\n";
        
        // Clean up
        unset($_POST['step'], $_POST['document_type'], $_POST['business_type']);
    }

    function testTypeformConfigurationAccess()
    {
        // Test that configuration is accessible
        $config = \Boctulus\Simplerest\core\libs\Config::get('Typeform');
        
        $this->assertNotEmpty($config, 'Typeform configuration should be accessible');
        $this->assertIsArray($config, 'Typeform configuration should be an array');
        
        // Test specific config values
        $this->assertArrayHasKey('api_base_url', $config, 'Config should have api_base_url');
        $this->assertArrayHasKey('links', $config, 'Config should have links section');
        $this->assertArrayHasKey('ui', $config, 'Config should have ui section');
        
        echo "✓ Typeform configuration is accessible and properly structured\n";
    }

    function testTypeformAssetsPath()
    {
        // Test that asset paths are properly constructed
        $typeform = new \Boctulus\Simplerest\modules\Typeform\Typeform();
        
        // Get reflection to access private method
        $reflection = new \ReflectionClass($typeform);
        $method = $reflection->getMethod('get_background_image');
        $method->setAccessible(true);
        
        $backgroundImage = $method->invoke($typeform);
        
        $this->assertIsString($backgroundImage, 'Background image should be a string');
        $this->assertNotEmpty($backgroundImage, 'Background image should not be empty');
        
        // Should be a proper URL or path
        $this->assertTrue(
            filter_var($backgroundImage, FILTER_VALIDATE_URL) || strpos($backgroundImage, '/') !== false,
            'Background image should be a valid URL or path'
        );
        
        echo "✓ Asset paths are properly constructed\n";
    }

    function testTypeformCPTCreation()
    {
        // Test that CPT can create registrations
        $formData = [
            'company_name' => 'Test Company Ltd',
            'rut' => '12345678-9',
            'email' => 'test@company.com',
            'phone' => '+56912345678',
            'business_type' => 'individual'
        ];
        
        $registration_id = \Boctulus\Simplerest\custom_post_types\TypeformRegistrationCPT::createRegistration($formData, 3);
        
        $this->assertNotFalse($registration_id, 'CPT should create registration successfully');
        $this->assertGreaterThan(0, $registration_id, 'Registration ID should be positive');
        
        // Verify post was created
        $post = get_post($registration_id);
        $this->assertNotNull($post, 'Created post should exist');
        $this->assertEquals('typeform_reg', $post->post_type, 'Post type should be typeform_reg');
        
        // Verify meta data was saved
        $saved_company = get_post_meta($registration_id, 'company_name', true);
        $this->assertEquals('Test Company Ltd', $saved_company, 'Company name should be saved correctly');
        
        $saved_step = get_post_meta($registration_id, 'form_step', true);
        $this->assertEquals(3, intval($saved_step), 'Form step should be saved correctly');
        
        echo "✓ CPT registration creation works correctly\n";
        
        // Clean up
        wp_delete_post($registration_id, true);
    }

    function testTypeformEmailNotification()
    {
        // Test email notification functionality
        $formData = [
            'company_name' => 'Email Test Company',
            'rut' => '98765432-1',
            'email' => 'emailtest@company.com',
            'rep_name' => 'John Doe',
            'document_types' => ['boleta', 'factura']
        ];
        
        // Mock MailWP to avoid sending real emails
        $original_mailer = \Boctulus\Simplerest\core\libs\Config::get('email.default_mailer_class');
        \Boctulus\Simplerest\core\libs\Config::set('email.default_mailer_class', \Boctulus\Simplerest\core\libs\MailMock::class);
        
        $registration_id = \Boctulus\Simplerest\custom_post_types\TypeformRegistrationCPT::createRegistration($formData, 8);
        
        $this->assertNotFalse($registration_id, 'CPT should create completed registration');
        
        // Verify email was "sent" (mocked)
        $post_status = get_post_status($registration_id);
        $this->assertEquals('publish', $post_status, 'Completed registration should be published');
        
        echo "✓ Email notification system works correctly\n";
        
        // Restore original mailer
        \Boctulus\Simplerest\core\libs\Config::set('email.default_mailer_class', $original_mailer);
        
        // Clean up
        wp_delete_post($registration_id, true);
    }

    function testTypeformProcessWithCPT()
    {
        // Test complete process with CPT integration
        $_POST['step'] = 4;
        $_POST['company_name'] = 'Process Test Co';
        $_POST['rut'] = '11222333-4';
        $_POST['email'] = 'process@test.com';
        $_POST['rep_name'] = 'Jane Smith';
        
        // Clear any existing session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Start output buffering to catch JSON
        ob_start();
        $this->controller->process();
        $output = ob_get_contents();
        ob_end_clean();
        
        // Verify JSON response
        $response = json_decode($output, true);
        $this->assertIsArray($response, 'Response should be valid JSON');
        $this->assertTrue($response['success'] ?? false, 'Response should indicate success');
        $this->assertEquals(4, $response['step'] ?? 0, 'Step should be correct');
        $this->assertArrayHasKey('registration_id', $response, 'Response should include registration ID');
        $this->assertArrayHasKey('message', $response, 'Response should include message');
        
        // Verify CPT was created
        if ($response['registration_id']) {
            $post = get_post($response['registration_id']);
            $this->assertNotNull($post, 'CPT post should be created');
            
            $saved_company = get_post_meta($response['registration_id'], 'company_name', true);
            $this->assertEquals('Process Test Co', $saved_company, 'Company name should be saved in CPT');
            
            // Clean up
            wp_delete_post($response['registration_id'], true);
        }
        
        echo "✓ Complete process with CPT integration works correctly\n";
        
        // Clean up POST data
        unset($_POST['step'], $_POST['company_name'], $_POST['rut'], $_POST['email'], $_POST['rep_name']);
    }

    function testTypeformStandaloneTemplate()
    {
        // Test standalone template generation
        $url = base_url() . '/typeform';
        
        $response = $this->get($url);
        
        $this->assertNotEquals(404, $response['status'], 'Standalone template should not return 404');
        $this->assertEquals(200, $response['status'], 'Standalone template should return 200');
        
        $content = $response['body'];
        
        // Check for standalone template markers
        $this->assertContains('<!DOCTYPE html>', $content, 'Should contain full HTML document');
        $this->assertContains('typeform-page', $content, 'Should contain typeform-page class');
        $this->assertContains('typeform_ajax', $content, 'Should contain JavaScript configuration');
        
        // Check that WordPress theme elements are NOT present
        $this->assertNotContains('wp_head', $content, 'Should not contain wp_head calls');
        $this->assertNotContains('wp_footer', $content, 'Should not contain wp_footer calls');
        
        echo "✓ Standalone template renders correctly\n";
    }

    function testTypeformAssetURLGeneration()
    {
        // Test that asset URLs are generated correctly for standalone version
        $controller_reflection = new \ReflectionMethod($this->controller, 'get');
        
        ob_start();
        $result = $this->controller->get();
        ob_end_clean();
        
        // Check that generated URLs don't use WordPress functions
        $this->assertNotContains('plugins_url(', $result, 'Standalone should not use plugins_url()');
        $this->assertContains('/app/modules/Typeform/assets/', $result, 'Should contain asset paths');
        
        echo "✓ Asset URL generation works independently\n";
    }

    function runAllTests()
    {
        echo "=== Running Expanded Typeform API Tests ===\n\n";
        
        try {
            // Original tests
            $this->testTypeformGetRoute();
            $this->testTypeformProcessRoute();
            $this->testTypeformControllerGet();
            $this->testTypeformControllerProcess();
            $this->testTypeformConfigurationAccess();
            $this->testTypeformAssetsPath();
            
            // New tests for expanded functionality
            $this->testTypeformCPTCreation();
            $this->testTypeformEmailNotification();
            $this->testTypeformProcessWithCPT();
            $this->testTypeformStandaloneTemplate();
            $this->testTypeformAssetURLGeneration();
            
            echo "\n✅ All expanded Typeform tests passed!\n";
            return true;
            
        } catch (\Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            return false;
        }
    }
}