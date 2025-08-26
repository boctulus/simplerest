<?php

namespace Boctulus\Simplerest\Modules\Typeform;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Session;

class Typeform
{
    static function get()
    {
        // Load CSS files
        css_file(__DIR__ . '/assets/css/typeform.css');
        
        // Load JS files in correct dependency order
        js_file(__DIR__ . '/assets/js/validation.js', null, true);
        js_file(__DIR__ . '/assets/js/data-persistence.js', null, true);
        js_file(__DIR__ . '/assets/js/form-handlers.js', null, true);
        js_file(__DIR__ . '/assets/js/step-manager.js', null, true);
        js_file(__DIR__ . '/assets/js/form-submission.js', null, true);
        js_file(__DIR__ . '/assets/js/typeform.js', null, true);

        // Initialize session for form data
        Session::start();
        
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
        
        // Process background image path
        if (!filter_var($backgroundImage, FILTER_VALIDATE_URL) && !str_starts_with($backgroundImage, '/')) {
            // Relative path from assets/img/
            $backgroundImage = __DIR__ . '/assets/img/' . $backgroundImage;
            // Convert to web path
            $backgroundImage = str_replace(ROOT_PATH, '', $backgroundImage);
            $backgroundImage = str_replace('\\', '/', $backgroundImage);
        }
        
        return get_view(__DIR__ . '/views/typeform.php', [
            'tos_link' => $tosLink,
            'background_image' => $backgroundImage,
            'brand_title' => $brandTitle,
            'brand_subtitle' => $brandSubtitle
        ]);
    }

    static function process()
    {
        Session::start();
        
        $step = $_POST['step'] ?? 1;
        $data = $_POST;
        
        // Store form data in session
        if (!isset($_SESSION['typeform_data'])) {
            $_SESSION['typeform_data'] = [];
        }
        
        $_SESSION['typeform_data'] = array_merge($_SESSION['typeform_data'], $data);
        
        // Return JSON response for AJAX handling
        header('Content-Type: application/json');
        
        return json_encode([
            'success' => true,
            'step' => $step,
            'data' => $_SESSION['typeform_data']
        ]);
    }
}