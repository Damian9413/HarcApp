<?php

//Singleton: jedna instancja na cały request (BINGO D1).

class UserRepository
{
    private static ?UserRepository $instance = null;

    private PDO $database;

    private function __construct()
    {
        $this -> database = Database::connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    //szuka po mailu - return wiersz ;lub null (A1)

    public function getUserByEmail(string $email): ?array
    {
        //szablon
        $stmt = $this->database->prepare('SELECT id, email, password_hash, name, role, is_approved FROM users WHERE email = :email'); // placeholder :email → mail później
        $stmt->bindParam(':email', $email, PDO::PARAM_STR); // bind – podpiecie danych ze zmiennej $email
        $stmt->execute(); // wysyłamy zapytanie do bazy
        $row = $stmt->fetch(PDO::FETCH_ASSOC); // fetch – pobieramy wiersz; FETCH_ASSOC – zwraca wiersz jako tablicę asocjacyjną
        return $row ?: null;
    }

    public function addUser(string $email, string $passwordHash, string $name, string $role = 'uczestnik'): bool
    {
        $stmt = $this->database->prepare('INSERT INTO users (email, password_hash, name, role, is_approved) VALUES (:email, :password_hash, :name, :role, :is_approved)');
        $isApproved = false;
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':is_approved', $isApproved, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

}