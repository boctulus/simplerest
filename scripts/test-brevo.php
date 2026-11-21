<?php

use Boctulus\Simplerest\Core\Libs\Brevo;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Mail\Mail;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/app.php';

$transport = $argv[1] ?? '';
if (!in_array($transport, ['api', 'smtp'], true)) {
    fwrite(STDERR, "Usage: php scripts/test-brevo.php api|smtp\nSends one test email to boctulus@gmail.com.\n");
    exit(2);
}

$subject = 'SimpleRest Brevo test ' . strtoupper($transport) . ' ' . gmdate('Y-m-d H:i:s') . ' UTC';
$body = '<p>Prueba de correo de SimpleRest mediante Brevo (' . strtoupper($transport) . ').</p>';

if ($transport === 'api') {
    $ok = Brevo::send('boctulus@gmail.com', $subject, $body, null, [], [], [], [], 'Prueba de correo de SimpleRest mediante Brevo API.');
    echo json_encode(['transport' => 'api', 'accepted' => $ok, 'messageId' => Brevo::messageId(), 'error' => Brevo::errors()], JSON_UNESCAPED_SLASHES) . PHP_EOL;
} else {
    // SMTP debug transcripts contain message contents and authentication details.
    $smtpAcceptance = null;
    $options = Config::get('email.mailers.brevo');
    $options['Debugoutput'] = static function ($message, $level) use (&$smtpAcceptance) {
        if (str_contains($message, 'SERVER -> CLIENT:') && preg_match('/250[ -].*(?:queued|2\.0\.0)[^\r\n]*/i', $message, $matches)) {
            $smtpAcceptance = $matches[0];
        }
    };
    Config::set('email.mailers.brevo', $options);
    Mail::debug(2);
    if (in_array('--diagnose', $argv, true)) {
        $options = Config::get('email.mailers.brevo');
        $captureAcceptance = $options['Debugoutput'];
        $options['Debugoutput'] = static function ($message, $level) use ($captureAcceptance) {
            $captureAcceptance($message, $level);
            if (preg_match('/certificate|SSL operation|Failed to enable crypto|Connection failed|SMTP ERROR/i', $message)) {
                foreach (['Username', 'Password'] as $field) {
                    $secret = (string) Config::get('email.mailers.brevo.' . $field, '');
                    if ($secret !== '') {
                        $message = str_replace([$secret, base64_encode($secret)], '[redacted]', $message);
                    }
                }
                fwrite(STDERR, trim($message) . PHP_EOL);
            }
        };
        Config::set('email.mailers.brevo', $options);
        Mail::debug(3);
    }
    Mail::setMailer('brevo');
    $ok = Mail::send('boctulus@gmail.com', $subject, $body);
    $error = Mail::errors();
    $password = (string) Config::get('email.mailers.brevo.Password', '');
    if ($password !== '' && is_string($error)) {
        $error = str_replace($password, '[redacted]', $error);
    }
    echo json_encode([
        'transport' => 'smtp', 'accepted' => $ok,
        'deliveryConfirmed' => false,
        'from' => Config::get('email.from.address'),
        'to' => 'boctulus@gmail.com', 'subject' => $subject,
        'smtpAcceptance' => $smtpAcceptance, 'error' => $error,
    ], JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
exit($ok ? 0 : 1);
