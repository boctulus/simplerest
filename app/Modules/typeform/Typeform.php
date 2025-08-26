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
        
        return get_view(__DIR__ . '/views/typeform.php', [
            'tos_link' => $tosLink
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