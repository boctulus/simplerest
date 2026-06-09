<?php

namespace Boctulus\Simplerest\Core\Security\Compiler;

final class TbPermissionBits
{
    public const LIST_ALL = 64;
    public const SHOW_ALL = 32;
    public const LIST     = 16;
    public const SHOW     = 8;
    public const CREATE   = 4;
    public const UPDATE   = 2;
    public const DELETE   = 1;

    public const ACTIONS = [
        'list_all' => self::LIST_ALL,
        'show_all' => self::SHOW_ALL,
        'list'     => self::LIST,
        'show'     => self::SHOW,
        'create'   => self::CREATE,
        'update'   => self::UPDATE,
        'delete'   => self::DELETE,
    ];

    public static function bitFor(string $action): int
    {
        return self::ACTIONS[$action] ?? 0;
    }

    /**
     * @return array<string, bool> ['action' => granted]
     */
    public static function unpack(int $packed): array
    {
        $out = [];
        foreach (self::ACTIONS as $action => $bit) {
            $out[$action] = ($packed & $bit) !== 0;
        }
        return $out;
    }

    /**
     * @return array<string, true> only granted actions, hash-map shape
     */
    public static function unpackGranted(int $packed): array
    {
        $out = [];
        foreach (self::ACTIONS as $action => $bit) {
            if (($packed & $bit) !== 0) {
                $out[$action] = true;
            }
        }
        return $out;
    }
}
