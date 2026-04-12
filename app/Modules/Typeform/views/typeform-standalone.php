<?php
// Typeform completely standalone template - No WordPress dependencies
// Similar to SimpleRest tpl_bt3.php structure

// Include required helper functions
require_once __DIR__ . '/../../../core/helpers/view.php';

// Initialize variables for views
$tos_link = '#'; // Terms of service link
$background_image = '';
$brand_title = 'Solicitud eFirma';
$brand_subtitle = 'Documentos Tributarios Electrónicos';

// Prepare variables for views
$viewVars = compact('tos_link', 'background_image', 'brand_title', 'brand_subtitle');

// Include all step partial views dynamically
$typeform_content = '';
$steps_directory = __DIR__ . '/steps/';

// Get all step files and sort them alphabetically
$step_files = glob($steps_directory . 'step-*.php');
if ($step_files === false) {
    $step_files = [];
}

// Sort alphabetically by filename
sort($step_files);

// Load each step view and update data-step attributes dynamically
$step_counter = 1;
foreach ($step_files as $step_file) {
    // Use include_no_render directly - get_view() has issues with realpath
    $step_content = include_no_render($step_file, $viewVars);
    
    // Extract step-alias if exists in the content
    $step_alias = null;
    if (preg_match('/data-step-alias=(["\'])([^"\']*)\1/', $step_content, $matches)) {
        $step_alias = $matches[2];
    }
    
    // Replace data-step with dynamic step number
    $step_content = preg_replace('/data-step=(["\'])\d+\1/', 'data-step="' . $step_counter . '"', $step_content);
    
    // Add step-alias if it doesn't exist but we extracted one from a comment or attribute
    if ($step_alias && !preg_match('/data-step-alias=/', $step_content)) {
        $step_content = preg_replace('/data-step=(["\'])\d+\1/', 'data-step="' . $step_counter . '" data-step-alias="' . $step_alias . '"', $step_content);
    }
    
    // Ensure first step has 'active' class and remove 'active' from others
    if ($step_counter === 1) {
        // Add active class to first step if not present
        if (!preg_match('/class="[^"]*active[^"]*"/', $step_content)) {
            $step_content = preg_replace('/class="([^"]*step[^"]*)"/', 'class="$1 active"', $step_content);
        }
    } else {
        // Remove active class from other steps
        $step_content = preg_replace('/class="([^"]*)\s*active\s*([^"]*)"/', 'class="$1 $2"', $step_content);
        $step_content = preg_replace('/class="\s+/', 'class="', $step_content); // Clean up extra spaces
        $step_content = preg_replace('/\s+class="/', ' class="', $step_content); // Clean up extra spaces
    }
    
    $typeform_content .= $step_content;
    $step_counter++;
}

$title = 'Typeform - Activación Boletas Electrónicas';

// Get base URL for assets (independent from WordPress)  
// Simple approach: construct URL from known structure
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$plugin_base_url = $base_url . '/wp-content/plugins/efirma';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- jQuery -->
    <script src="<?= $plugin_base_url ?>/assets/third_party/jquery/3.x/jquery.min.js"></script>

    <!-- Typeform CSS -->
    <link rel="stylesheet" href="<?= $plugin_base_url ?>/app/modules/Typeform/assets/css/typeform.css">

</head>
<body class="typeform-page">
    <main>
        <div class="typeform-container">
            <!-- Progress bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>

             <!-- Header with Logo (movido dentro de main para que no desplace el contenido hacia abajo) -->
            <header class="typeform-header" style="text-align: center; padding: 10px 0; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                <img src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/img/logo-large-empty.png" alt="Logo" style="max-height: 50px; height: auto;">
            </header>

            <!-- Form container -->
            <div class="form-wrapper">
                <form id="typeform" method="POST">
                    <input type="hidden" id="currentStep" name="step" value="1">
                    
                    <?= $typeform_content ?>
                    
                </form>
            </div>
        </div>
    </main>

    <footer id="footer">
        <!-- Forms library (required by data-persistence) -->
        <script src="<?= $plugin_base_url ?>/assets/js/forms.js"></script>
        
        <!-- Clean Architecture Core Modules (load first) -->
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/core/dependency-manager.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/core/form-data-provider.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/core/step-manager-generic.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/core/typeform-step-manager.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/core/integration-layer.js"></script>
        
        <!-- Legacy modules (for backward compatibility) -->
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/validation.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/rut-formatter.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/document-types-logic.js"></script>
        <!-- <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/conditional-steps.js"></script> -->
        <!-- New simple conditional logic -->
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/step-conditional-logic.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/step-navigation.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/form-summary-simple.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/floating-controls.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/data-persistence.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/form-handlers.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/step-manager.js"></script>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/form-submission.js"></script>
        
        <!-- Main Controller (loads after all dependencies) -->
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/typeform.js"></script>

        <script>
        // Standalone AJAX configuration (no WordPress dependencies)
        const typeform_ajax = {
            ajaxurl: '<?= $base_url ?>/typeform/submit',
            api_base_url: '<?= \Boctulus\Simplerest\Core\Libs\Config::get('Typeform.api_base_url') ?? $plugin_base_url ?>'
        };
        </script>
    </footer>
</body>
</html>