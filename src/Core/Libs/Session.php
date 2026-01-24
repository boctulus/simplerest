<?php

namespace Boctulus\Simplerest\Core\Libs;

class Session 
{
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key = null, $default = null) {
        self::start();
        
        if ($key === null) {
            return $_SESSION;
        }
        
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function delete($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function clear() {
        self::start();
        session_unset();
    }

    public static function destroy() {
        self::start();
        session_destroy();
    }

    public static function regenerateId($delete_old_session = false) {
        self::start();
        session_regenerate_id($delete_old_session);
    }

    public static function getId() {
        self::start();
        return session_id();
    }

    public static function getName() {
        return session_name();
    }

    public static function setName($name) {
        session_name($name);
    }

    public static function flash($key, $value = null) {
        self::start();
        
        if ($value === null) {
            // Get flash message and remove it
            $message = $_SESSION['_flash'][$key] ?? null;
            if (isset($_SESSION['_flash'][$key])) {
                unset($_SESSION['_flash'][$key]);
            }
            return $message;
        } else {
            // Set flash message
            if (!isset($_SESSION['_flash'])) {
                $_SESSION['_flash'] = [];
            }
            $_SESSION['_flash'][$key] = $value;
        }
    }

    public static function hasFlash($key) {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
}