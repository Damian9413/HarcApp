<?php

require_once __DIR__ . '/../repository/UserRepository.php';

class AdminController
{
    public function index(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        $userEmail = $_SESSION['user_email'] ?? '';
        $userRole = $_SESSION['user_role'] ?? 'admin';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $repo = UserRepository::getInstance();
        $approvedUsers = $repo->getApprovedUsers();
        $usersToApprove = $repo->getUnapprovedUsers();
        $sidebarActive = 'admin';
        require __DIR__ . '/../views/admin/index.php';
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

    public function approve(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin');
            exit;
        }
        UserRepository::getInstance()->approveUser($id);
        header('Location: /admin');
        exit;
    }

    /** Zmiana roli użytkownika (POST: id, role). */
    public function changeRole(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $role = trim($_POST['role'] ?? $_GET['role'] ?? '');
        if ($id <= 0 || $role === '') {
            header('Location: /admin');
            exit;
        }
        UserRepository::getInstance()->updateUserRole($id, $role);
        header('Location: /admin');
        exit;
    }

    /** Usunięcie użytkownika (GET: id). Nie można usunąć samego siebie. */
    public function delete(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin');
            exit;
        }
        if ($id === (int) $_SESSION['user_id']) {
            header('Location: /admin');
            exit;
        }
        UserRepository::getInstance()->deleteUser($id);
        header('Location: /admin');
        exit;
    }
}