<?php
// Prepare variables for views
$viewVars = compact('tos_link', 'background_image', 'brand_title', 'brand_subtitle');

// Include all step partial views
$content = '';
$content .= get_view(__DIR__ . '/steps/step-1-welcome.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-2-document-types.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-3-business-info.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-4-legal-representative.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-5-electronic-signature.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-6-upload-documents.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-7-review-submit.php', $viewVars);
$content .= get_view(__DIR__ . '/steps/step-8-thank-you.php', $viewVars);

// Include the template with content
include __DIR__ . '/template.php';
?>