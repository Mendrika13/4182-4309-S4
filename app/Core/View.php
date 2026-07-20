<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/Views/' . $view . '.php';
        require $viewFile;
    }

    public static function esc(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function baseUrl(string $path = ''): string
    {
        $base = env('APP_URL', '');
        if ($base !== '') {
            return rtrim($base, '/') . '/' . ltrim($path, '/');
        }

        $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }

    public static function argent(float $montant): string
    {
        return number_format($montant, 0, ',', ' ');
    }
}
