<?php

namespace App\Core;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function connexion(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $dbPath = ROOT_PATH . '/' . env('DB_PATH', 'writable/mobile_money.db');
        $baseSql = ROOT_PATH . '/' . env('BASE_SQL_PATH', 'base.sql');
        $premierLancement = ! is_file($dbPath);

        $dir = dirname($dbPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        if ($premierLancement && is_file($baseSql)) {
            $pdo->exec(file_get_contents($baseSql));
        }

        self::$instance = $pdo;

        return self::$instance;
    }
}
