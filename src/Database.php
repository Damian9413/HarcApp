<?php

//klasa do jednego wspólnego połączenia do bazy danych

class Database{
    //instacja PDO - null dopóku nie wywołmy connect()
    private static ?PDO $connection = null;

    public static function connect(): PDO{
        if (self::$connection === null){
            $config = require __DIR__ . '/../config/database.php';

            //DSN - gdzie baza
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $config['host'],
                $config['port'],
                $config['dbname']
            );
            // Tworzymy obiekt PDO – to właściwe połączenie z bazą.
            self::$connection = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                [
                    // ERRMODE_EXCEPTION = błędy SQL rzucają wyjątek (łatwiej łapać).
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        }
        return self::$connection;
}
}