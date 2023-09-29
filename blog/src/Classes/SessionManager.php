<?php

namespace Zitro\Blog\Classes;

class SessionManager {
    public static function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public static function remove($key): void
    {
        unset($_SESSION[$key]);
    }
}
