<?php

// singleton do bazy

class Database{
    private static ?PDO $connection = null;

    public static function connect(): PDO{
        if (self::$connection === null){
            $config = require __DIR__ . '/../config/database.php';

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $config['host'],
                $config['port'],
                $config['dbname']
            );
            
            self::$connection = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$connection;
    }
}
