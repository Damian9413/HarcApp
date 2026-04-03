<?php

// singleton

class UserRepository
{
    private static ?UserRepository $instance = null;
    private PDO $database;

    private function __construct()
    {
        $this->database = Database::connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    // szuka po mailu
    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->database->prepare('SELECT id, email, password_hash, name, role, is_approved FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
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

    // oczekujacy na zatwierdzenie
    public function getUnapprovedUsers(): array
    {
        $stmt = $this->database->prepare('SELECT id, email, name, role, is_approved FROM users WHERE is_approved = false ORDER BY id');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveUser(int $id): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET is_approved = true WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    // zatwierdzeni
    public function getApprovedUsers(): array
    {
        $stmt = $this->database->prepare('SELECT id, email, name, role, is_approved FROM users WHERE is_approved = true ORDER BY email');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // zmiana roli
    public function updateUserRole(int $id, string $role): bool
    {
        // mapowanie polskich znakow
        $roleMap = [
            'twórca' => 'tworca',
            'uczestnik' => 'uczestnik',
            'punktowy' => 'punktowy',
            'admin' => 'admin'
        ];
        
        $dbRole = $roleMap[$role] ?? $role;
        
        $allowed = ['uczestnik', 'punktowy', 'tworca', 'admin'];
        if (!in_array($dbRole, $allowed, true)) {
            return false;
        }
        $stmt = $this->database->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->bindParam(':role', $dbRole, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->database->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->database->prepare('SELECT id, email, password_hash, name, role, is_approved FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateUserName(int $id, string $name): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET name = :name WHERE id = :id');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->database->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->bindParam(':hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }
}
