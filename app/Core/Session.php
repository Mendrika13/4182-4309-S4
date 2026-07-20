<?php

namespace App\Core;

class Session
{
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function remove(array $keys): void
    {
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        if (! isset($_SESSION['_flash'][$type])) {
            return null;
        }

        $message = $_SESSION['_flash'][$type];
        unset($_SESSION['_flash'][$type]);

        return $message;
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function verifierCsrf(?string $token): bool
    {
        return is_string($token) && ! empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}
