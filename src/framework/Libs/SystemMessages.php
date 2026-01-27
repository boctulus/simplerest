<?php

namespace Boctulus\Simplerest\Core\Libs;

class SystemMessages
{
    protected static array $messages;

    public static function get(string $code, ...$args): string
    {
        self::load();
        $msg = self::$messages[$code]['text'] ?? self::$messages[$code] ?? $code;

        // Intentar traducir si gettext está disponible
        if (function_exists('_')) {
            $translated = _($msg);
            // Solo usar traducción si no está vacía
            if (!empty($translated)) {
                $msg = $translated;
            }
        }
        
        return !empty($args) ? vsprintf($msg, $args) : $msg;
    }

    protected static function load(): void
    {
        if (!isset(self::$messages)) {
            self::$messages = require CONFIG_PATH . 'messages.php';
        }
    }
}

