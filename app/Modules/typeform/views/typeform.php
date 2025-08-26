<?php
// Include all step partial views
$content = '';
$content .= get_view(__DIR__ . '/steps/step-1-welcome.php');
$content .= get_view(__DIR__ . '/steps/step-2-document-types.php');
$content .= get_view(__DIR__ . '/steps/step-3-business-info.php');
$content .= get_view(__DIR__ . '/steps/step-4-legal-representative.php');
$content .= get_view(__DIR__ . '/steps/step-5-electronic-signature.php');
$content .= get_view(__DIR__ . '/steps/step-6-upload-documents.php');
$content .= get_view(__DIR__ . '/steps/step-7-review-submit.php');
$content .= get_view(__DIR__ . '/steps/step-8-thank-you.php');

// Include the template with content
include __DIR__ . '/template.php';
?>