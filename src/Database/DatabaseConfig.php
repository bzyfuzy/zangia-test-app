<?php

namespace App\Database;

class DatabaseConfig
{
    public static function getSettings(): array
    {
        return [
            'host'    => $_ENV["DB_HOST"],
            'dbname'  => $_ENV["DB_NAME"],
            'user'    => $_ENV["DB_USER"],
            'pass'    => $_ENV["DB_PASSWORD"],
            'charset' => 'utf8mb4',
        ];
    }
}
