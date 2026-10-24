<?php

namespace Boctulus\Simplerest\Mail;

use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Mail\Interfaces\IMail;

/**
 * Audit mailer — captures every send() call to disk without delivering.
 * Files land in src/logs/mail/ as JSON (see logDir()).
 */
class MailAuditor extends MailBase implements IMail
{
    /*
        Same directory as when this class lived in the core
        (src/framework/Libs): the src/ folder of the SimpleRest core.
    */
    private static function logDir(): string
    {
        $coreLibs = dirname((new \ReflectionClass(Logger::class))->getFileName());

        return dirname($coreLibs, 2) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'mail';
    }

    static function send(
        $to,
        $subject      = '',
        $body         = '',
        $attachments  = null,
        $from         = [],
        array $cc     = [],
        $bcc          = [],
        $reply_to     = [],
        $alt_body     = null
    ): bool {
        $dir = static::logDir();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $entry = [
            'timestamp'   => date('Y-m-d H:i:s'),
            'to'          => $to,
            'subject'     => $subject,
            'body'        => $body,
            'from'        => $from,
            'cc'          => $cc,
            'bcc'         => $bcc,
            'reply_to'    => $reply_to,
            'attachments' => $attachments,
            'alt_body'    => $alt_body,
        ];

        $filename = date('Y-m-d_H-i-s') . '_' . substr(md5(uniqid('', true)), 0, 6) . '.json';

        file_put_contents(
            $dir . DIRECTORY_SEPARATOR . $filename,
            json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return true;
    }

    /** Returns the N most recent log entries as ['file' => ..., 'data' => ...] */
    static function recent(int $limit = 10): array
    {
        $dir = static::logDir();

        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.json');

        if (!$files) {
            return [];
        }

        rsort($files);
        $files = array_slice($files, 0, $limit);

        return array_map(function (string $path) {
            return [
                'file' => basename($path),
                'data' => json_decode(file_get_contents($path), true),
            ];
        }, $files);
    }
}
