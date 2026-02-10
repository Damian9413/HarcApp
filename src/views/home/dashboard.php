<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel główny – HarcApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hubballi&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <div class="dashboard-page">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <!-- Logo HarcApp -->
            <div class="dashboard-logo">
                <span class="iconify" data-icon="ph:compass-fill" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <span class="dashboard-logo-text">HarcApp</span>
            </div>

            <!-- Nawigacja -->
            <nav class="dashboard-nav">
                <a href="/" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'dashboard' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:view-dashboard" data-width="48" data-height="48"></span>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="dashboard-nav-item">
                    <span class="iconify" data-icon="mdi:gamepad-variant-outline" data-width="48" data-height="48"></span>
                    <span>Gry</span>
                </a>
                <?php if (isset($userRole) && $userRole === 'admin'): ?>
                <a href="/admin" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'admin' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:account-group-outline" data-width="48" data-height="48"></span>
                    <span>Użytkownicy</span>
                </a>
                <?php endif; ?>
                <a href="#" class="dashboard-nav-item">
                    <span class="iconify" data-icon="mdi:cog-outline" data-width="48" data-height="48"></span>
                    <span>Ustawienia</span>
                </a>
            </nav>

            <!-- Użytkownik na dole sidebaru -->
            <div class="dashboard-user">
                <span class="iconify dashboard-user-icon" data-icon="mdi:account-circle" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <div class="dashboard-user-info">
                    <span class="dashboard-user-role"><?= htmlspecialchars($userRoleLabel ?? $userRole ?? 'Użytkownik') ?></span>
                    <span class="dashboard-user-email"><?= htmlspecialchars($userEmail ?? '') ?></span>
                </div>
            </div>
        </aside>

        <!-- Główna treść -->
        <main class="dashboard-main">
            <header class="dashboard-header">
                <h1 class="dashboard-title">Panel Główny</h1>
                <a href="/security/logout" class="dashboard-header-link">
                    <span class="iconify" data-icon="mdi:logout" data-width="24" data-height="24" style="color: #fff;"></span>
                    <span>Wyloguj się</span>
                </a>
            </header>

            <!-- Karty akcji -->
            <div class="dashboard-cards">
                <a href="#" class="panel-card dashboard-action-card">
                    <span class="iconify dashboard-action-icon" data-icon="mdi:map-outline" data-width="48" data-height="48" style="color: #FF383C;"></span>
                    <div>
                        <h2 class="dashboard-card-title">Moje Gry</h2>
                        <p class="dashboard-card-subtitle">Zobacz i zarządzaj</p>
                    </div>
                </a>
                <a href="#" class="panel-card dashboard-action-card">
                    <span class="iconify dashboard-action-icon" data-icon="mdi:plus-circle-outline" data-width="48" data-height="48" style="color: #FF383C;"></span>
                    <div>
                        <h2 class="dashboard-card-title">Stwórz Grę</h2>
                        <p class="dashboard-card-subtitle">Stwórz własną grę</p>
                    </div>
                </a>
            </div>
            <a href="<?= (isset($userRole) && $userRole === 'admin') ? '/admin' : '#' ?>" class="panel-card dashboard-action-card dashboard-action-card--wide">
                <span class="iconify dashboard-action-icon" data-icon="mdi:account-group-outline" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <div>
                    <h2 class="dashboard-card-title">Zarządzaj użytkownikami</h2>
                    <p class="dashboard-card-subtitle">Zarządzaj dostępami i rolami</p>
                </div>
            </a>


        </main>

        <!-- Nawigacja mobilna (dolny pasek) – widoczna tylko na mobile -->
        <nav class="dashboard-footer-nav" aria-label="Nawigacja główna">
            <a href="/" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'dashboard' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:view-dashboard" data-width="28" data-height="28"></span>
                <span>Dashboard</span>
            </a>
            <a href="#" class="dashboard-footer-nav-item">
                <span class="iconify" data-icon="mdi:gamepad-variant-outline" data-width="28" data-height="28"></span>
                <span>Gry</span>
            </a>
            <?php if (isset($userRole) && $userRole === 'admin'): ?>
            <a href="/admin" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'admin' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:account-group-outline" data-width="28" data-height="28"></span>
                <span>Użytkownicy</span>
            </a>
            <?php endif; ?>
            <a href="#" class="dashboard-footer-nav-item">
                <span class="iconify" data-icon="mdi:cog-outline" data-width="28" data-height="28"></span>
                <span>Ustawienia</span>
            </a>
        </nav>
    </div>
</body>
</html>