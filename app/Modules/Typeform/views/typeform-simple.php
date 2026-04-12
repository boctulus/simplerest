<?php
// Typeform standalone template - Similar to SimpleRest tpl_bt3.php
// Independent template not using WordPress theme

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- jQuery -->
    <script src="<?= plugins_url('assets/third_party/jquery/3.x/jquery.min.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>

    <!-- Typeform CSS -->
    <link rel="stylesheet" href="<?= plugins_url('app/modules/Typeform/assets/css/typeform.css', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>">

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
        <!-- Typeform JavaScript files in correct dependency order -->
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/validation.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/rut-formatter.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/document-types-logic.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <!-- <script src="<?= plugins_url('app/modules/Typeform/assets/js/conditional-steps.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script> -->
        <!-- New simple conditional logic -->
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/step-conditional-logic.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/step-navigation.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/form-summary-simple.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/floating-controls.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/data-persistence.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/form-handlers.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/step-manager.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/form-submission.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>
        <script src="<?= plugins_url('app/modules/Typeform/assets/js/typeform.js', \Boctulus\Simplerest\Core\Constants::ROOT_PATH . 'index.php') ?>"></script>

        <script>
        // Localize AJAX data
        const typeform_ajax = {
            ajaxurl: '<?= admin_url('admin-ajax.php') ?>',
            nonce: '<?= wp_create_nonce('typeform_ajax_nonce') ?>',
            api_base_url: '<?= \Boctulus\Simplerest\Core\Libs\Config::get('Typeform.api_base_url') ?>'
        };
        </script>
    </footer>
</body>
</html>