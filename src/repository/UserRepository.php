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




    /** Lista użytkowników oczekujących na zatwierdzenie (is_approved = false). */
    public function getUnapprovedUsers(): array
    {
        $stmt = $this->database->prepare('SELECT id, email, name, role, is_approved FROM users WHERE is_approved = false ORDER BY id');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Ustawia is_approved = true dla użytkownika o podanym id. */
    public function approveUser(int $id): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET is_approved = true WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    /** Lista zatwierdzonych użytkowników (is_approved = true). */
    public function getApprovedUsers(): array
    {
        $stmt = $this->database->prepare('SELECT id, email, name, role, is_approved FROM users WHERE is_approved = true ORDER BY email');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Zmienia rolę użytkownika. Dozwolone: uczestnik, punktowy, twórca, admin. */
    public function updateUserRole(int $id, string $role): bool
    {
        $allowed = ['uczestnik', 'punktowy', 'twórca', 'admin'];
        if (!in_array($role, $allowed, true)) {
            return false;
        }
        $stmt = $this->database->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    /** Usuwa użytkownika o podanym id. */
    public function deleteUser(int $id): bool
    {
        $stmt = $this->database->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    //Pobiera uzytkownika po id
    public function getUserById(int $id): ?array
    {
        $stmt = $this->database->prepare('SELECT id, email, password_hash, name, role, is_approved FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    /** Aktualizacja imienia (ustawienia – profil). */
    public function updateUserName(int $id, string $name): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET name = :name WHERE id = :id');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    /** Aktualizacja hasła (ustawienia – bezpieczeństwo). */
    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->bindParam(':hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

}