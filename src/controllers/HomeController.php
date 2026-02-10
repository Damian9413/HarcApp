<?php

class HomeController
{
    public function index(): void
    {
        if (isset($_SESSION['user_id'])) {
            // Zalogowany – pokazujemy panel główny (dashboard)
            $userEmail = $_SESSION['user_email'] ?? '';
            $userRole = $_SESSION['user_role'] ?? '';
            $userRoleLabel = $this->getRoleLabel($userRole);
            $sidebarActive = 'dashboard';
            require __DIR__ . '/../views/home/dashboard.php';
            return;
        }

        // Niezalogowany – przekierowanie na logowanie 
        header('Location: /security/login');
        exit;
    }

    /** Zwraca role. */
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