<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IMail;
use Boctulus\Simplerest\Core\Libs\MailBase;

/**
 * Audit mailer — captures every send() call to disk without delivering.
 * Files land in plugin_root/logs/mail/ as JSON.
 */
class MailAuditor extends MailBase implements IMail
{
    private static function logDir(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'mail';
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
