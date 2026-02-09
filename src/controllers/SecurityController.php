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
        echo '<h2>Logowanie</h2>';
        if ($error !== '') {
            echo '<p style="color: red;">' . htmlspecialchars($error) . '</p>';
        }
        echo '<form method="post" action="/security/login">';
        echo '<label>Email: <input type="email" name="email" value="' . htmlspecialchars($email ?? '') . '" required></label><br>'; // required – nie wyśle jeśli puste; name= → potem $_POST['email']
        echo '<label>Hasło: <input type="password" name="password" required></label><br>';
        echo '<button type="submit">Zaloguj</button>';
        echo '</form>';
        echo '<p><a href="/">Strona główna</a></p>';
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
}
