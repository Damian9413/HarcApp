<?php

requirence_once __DIR__ . '/../repository/UserRepository.php';

class SettingsController
{
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }
        $userID = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'] ?? '';
        $userRole = $_SESSION['user_role'] ?? '';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $sidebarActive = 'settings';
        
        $repo = UserRepository::getInstance(); //czy mamy repo
        $user = $repo->getUserById($userID);
        $userName = $user['name'] ?? ''; 

        //Zapisz imie
        if ($SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['name'])) {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $error = 'Imię nie może być puste.';
            } elseif (strlen($name) > 25) {
                $error = 'Imię nie może być dłuższe niż 25 znaków.';
            } else {
                $repo->updateUserName($userID, $name);
                $success = true;
            }
            header('Location: /settings');
            exit;
        }
        require __DIR__ . '/../views/settings/index.php';
    }

    public function changePassword(): void{
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }
        $userID = (int) $_SESSION['user_id'];
        $currnt = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $repeat = $_POST['repeat_password'] ?? '';
        $error = '';
        if (strlen($new) < 8) {
            $error = 'Nowe hasło musi mieć min. 8 znaków.';
        } elseif ($new !== $repeat) {
            $error = 'Hasła nie są identyczne.';
        } else {
            $repo = UserRepository::getInstance();
            $user = $repo->getUserById($userId);
            if (!$user || !password_verify($current, $user['password_hash'])) {
                $error = 'Aktualne hasło jest nieprawidłowe.';
            } else {
                $repo->updatePassword($userId, password_hash($new, PASSWORD_DEFAULT));
            }
        }
        if ($error !== '') {
            $_SESSION['settings_password_error'] = $error;
        }
        header('Location: /settings');
        exit;
    }
    private function getRoleLabel(string $role): string
    {
        $labels = [
            'admin'     => 'Administrator',
            'twórca'    => 'Twórca',
            'punktowy'  => 'Punktowy',
            'uczestnik' => 'Uczestnik',
        ];
        return $labels[$role] ?? $role;
    }
}