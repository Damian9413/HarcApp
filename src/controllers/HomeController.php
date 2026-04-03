<?php

class HomeController
{
    public function index(): void
    {
        if (isset($_SESSION['user_id'])) {
            $userEmail = $_SESSION['user_email'] ?? '';
            $userRole = $_SESSION['user_role'] ?? '';
            $userRoleLabel = $this->getRoleLabel($userRole);
            $sidebarActive = 'dashboard';
            require __DIR__ . '/../views/home/dashboard.php';
            return;
        }

        header('Location: /security/login');
        exit;
    }

    private function getRoleLabel(string $role): string
    {
        $labels = [
            'admin'     => 'Administrator',
            'tworca'    => 'Twórca',
            'punktowy'  => 'Punktowy',
            'uczestnik' => 'Uczestnik',
        ];
        return $labels[$role] ?? $role;
    }
}
