<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Session;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class TypeformController
{
    function __construct()
    {
        // Ensure proper HTTP status code for all responses
        // This prevents 404 errors that can occur in some server configurations
        http_response_code(200);
        header('X-Powered-By: FriendlyPOS');
    }
    function get()
    {
        Session::start();
        
        // Check if this is actually a form submission (has form data)
        if (!empty($_GET['step']) || !empty($_POST['step'])) {
            return $this->process();
        }
        
        // Get configuration
        $config = Config::get('typeform');
        $tosLink = $config['links']['tos'] ?? '#';
        
        // Handle relative URLs
        if ($tosLink !== '#' && !filter_var($tosLink, FILTER_VALIDATE_URL)) {
            // If it's not a full URL, make it relative to the current domain
            $tosLink = rtrim(request_url(), '/') . '/' . ltrim($tosLink, '/');
        }
        
        // Get UI configuration
        $backgroundImage = $config['ui']['background_image'] ?? 'blue-pos.jpeg';
        $brandTitle = $config['ui']['brand']['title'] ?? 'Bienvenido';
        $brandSubtitle = $config['ui']['brand']['subtitle'] ?? 'Sistema de activación de boletas electrónicas';
        
        // Process background image path - standalone version
        if (!filter_var($backgroundImage, FILTER_VALIDATE_URL) && !str_starts_with($backgroundImage, '/')) {
            // Simple approach: construct URL from known structure  
            $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
            $backgroundImage = $base_url . '/wp-content/plugins/efirma/app/modules/Typeform/assets/img/' . $backgroundImage;
        }
        
        // For direct route access, use standalone template (no WordPress dependencies)
        return get_view(__DIR__ . '/../modules/Typeform/views/typeform-standalone.php', [
            'tos_link' => $tosLink,
            'background_image' => $backgroundImage,
            'brand_title' => $brandTitle,
            'brand_subtitle' => $brandSubtitle
        ]);
    }

    function process()
    {
        Session::start();
        
        // Handle both POST and GET requests
        $step = $_POST['step'] ?? $_GET['step'] ?? 1;
        $data = !empty($_POST) ? $_POST : $_GET;
        
        // Store form data in session
        if (!isset($_SESSION['typeform_data'])) {
            $_SESSION['typeform_data'] = [];
        }
        
        $_SESSION['typeform_data'] = array_merge($_SESSION['typeform_data'], $data);
        
        // Create or update typeform registration in database
        // try {
        //     $registration_id = TypeformRegistrationCPT::createRegistration($_SESSION['typeform_data'], $step);
            
        //     if ($registration_id) {
        //         $_SESSION['typeform_registration_id'] = $registration_id;
        //         $message = $step >= 8 ? 'Formulario completado y guardado exitosamente' : 'Datos guardados exitosamente';
        //     } else {
        //         $message = 'Datos procesados (sin guardar en BD)';
        //     }
        // } catch (\Exception $e) {
        //     // Log error but don't fail the process
        //     Logger::log("Error creating typeform registration: " . $e->getMessage());
        //     $message = 'Datos procesados (error al guardar)';
        // }
        
        // Return JSON response for AJAX handling
        http_response_code(200); // Explicitly set 200 OK
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        $response = [
            'success' => true,
            'step' => (int)$step,
            'data' => $_SESSION['typeform_data'],
            // 'message' => $message,
            'registration_id' => $_SESSION['typeform_registration_id'] ?? null,
            'timestamp' => date('Y-m-d H:i:s'),
            'status_code' => 200
        ];
        
        echo json_encode($response);
        
        exit;
    }
}
