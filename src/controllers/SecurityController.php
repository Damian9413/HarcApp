<?php

require_once __DIR__ . '/../repository/UserRepository.php';

class SecurityController
{
    public function login(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Pobieranie użytkowników z bazy
            $userRepository = UserRepository::getInstance(); // ta sama instancja (Singleton)
            $user = $userRepository->getUserByEmail($email); // zwraca wiersz

            if ($user === null || !password_verify($password, $user['password_hash'])) {
                $error = 'Nieprawidłowy email lub hasło';
            } elseif (!$user['is_approved']) {
                $error = 'Konto oczekuje na akceptację administratora.';
            } else {
                // Sesja musi być aktywna (start w index.php); na wszelki wypadek uruchom, gdy nie jest
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                session_regenerate_id(true); // nowy id – jak zmiana zamków
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: /');
                exit;
            }
        }

        // Formularz: przy GET lub przy POST z błędem
        require __DIR__ . '/../views/login.php';
    
    }

    public function logout(): void
    {
        // Sesja musi być aktywna, żeby ją zniszczyć – jeśli nie (np. wejście bez logowania), uruchom
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_unset();   // czyści $_SESSION (np. user_id, user_email)
        session_destroy(); // niszczy sesję (BINGO D5 – poprawne wylogowanie)
        header('Location: /');
        exit;
    }

    /**
     * register() – adres: /security/register
     * GET: formularz rejestracji. POST: walidacja, zapis użytkownika z is_approved = false.
     */
    public function register(): void
    {
        $error = '';
        $email = '';
        $name = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordRepeat = $_POST['password_repeat'] ?? '';
            $name = trim($_POST['name'] ?? '');

            // Walidacja długości (BINGO D2)
            if (strlen($email) > 50) {
                $error = 'Email za długi.';
            } elseif (strlen($password) > 50) {
                $error = 'Hasło za długie.';
            } elseif (strlen($name) > 25) {
                $error = 'Imię za długie.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Nieprawidłowy format email.';
            } elseif ($password !== $passwordRepeat) {
                $error = 'Hasła nie są takie same.';
            } elseif (strlen($password) < 8) {
                $error = 'Hasło musi mieć min. 8 znaków.';
            } else {
                $existing = UserRepository::getInstance()->getUserByEmail($email);
                if ($existing !== null) {
                    // Email w bazie – sprawdzamy, czy konto ma już zgodę admina
                    if (!$existing['is_approved']) {
                        $error = 'Konto z tym emailem istnieje i czeka na akceptację administratora.';
                    } else {
                        $error = 'Ten email jest już używany.';
                    }
                } else {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    UserRepository::getInstance()->addUser($email, $passwordHash, $name, 'uczestnik');
                    $success = true;
                    require __DIR__ . '/../views/register.php';
                    return;
                }
            }
        }

        // Formularz: przy GET lub przy POST z błędem – widok ze stylami
        require __DIR__ . '/../views/register.php';
    }
}
