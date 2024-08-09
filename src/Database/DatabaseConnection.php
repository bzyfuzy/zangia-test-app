<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $settings = DatabaseConfig::getSettings();
            $dsn = "mysql:host={$settings['host']};dbname={$settings['dbname']};charset={$settings['charset']}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $settings['user'], $settings['pass'], $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}
