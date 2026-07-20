<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

function env(string $key, $default = null)
{
    static $values = null;

    if ($values === null) {
        $values = [];
        $envFile = ROOT_PATH . '/.env';
        if (is_file($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') {
                    continue;
                }
                if (strpos($line, '=') === false) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $values[trim($k)] = trim($v);
            }
        }
    }

    return $values[$key] ?? $default;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
