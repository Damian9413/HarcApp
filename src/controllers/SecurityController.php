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

            $userRepository = UserRepository::getInstance();
            $user = $userRepository->getUserByEmail($email);

            if ($user === null || !password_verify($password, $user['password_hash'])) {
                $error = 'Nieprawidłowy email lub hasło';
            } elseif (!$user['is_approved']) {
                $error = 'Konto oczekuje na akceptację administratora.';
            } else {
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: /');
                exit;
            }
        }

        require __DIR__ . '/../views/login.php';
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /');
        exit;
    }

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

            // walidacja dlugosci
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

        require __DIR__ . '/../views/register.php';
    }

    // api do sprawdzania maila - fetch
    public function checkEmail(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? $_POST['email'] ?? '');

        if (empty($email)) {
            echo json_encode(['available' => false, 'message' => 'Email jest wymagany']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['available' => false, 'message' => 'Nieprawidłowy format email']);
            return;
        }

        $existing = UserRepository::getInstance()->getUserByEmail($email);
        
        if ($existing !== null) {
            echo json_encode(['available' => false, 'message' => 'Ten email jest już zajęty']);
        } else {
            echo json_encode(['available' => true, 'message' => 'Email dostępny']);
        }
    }
}
