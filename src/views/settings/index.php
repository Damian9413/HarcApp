<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ustawienia – HarcApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hubballi&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <div class="dashboard-page">
        <aside class="dashboard-sidebar">
            <div class="dashboard-logo">
                <span class="iconify" data-icon="ph:compass-fill" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <span class="dashboard-logo-text">HarcApp</span>
            </div>
            
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

                <a href="/settings" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'settings' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:cog-outline" data-width="48" data-height="48"></span>
                    <span>Ustawienia</span>
                </a>
            </nav>

            <div class="dashboard-user">
                <span class="iconify dashboard-user-icon" data-icon="mdi:account-circle" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <div class="dashboard-user-info">
                    <span class="dashboard-user-role"><?= htmlspecialchars($userRoleLabel ?? $userRole ?? '') ?></span>
                    <span class="dashboard-user-email"><?= htmlspecialchars($userEmail ?? '') ?></span>
                </div>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-header">
                <h1 class="dashboard-title">Ustawienia profilu</h1>
                <a href="/security/logout" class="dashboard-header-link">
                    <span class="iconify" data-icon="mdi:logout" data-width="24" data-height="24" style="color: #fff;"></span>
                    <span>Wyloguj się</span>
                </a>
            </header>

            <?php if (isset($_SESSION['settings_password_error'])): ?>
            <p class="settings-message settings-message--error"><?= htmlspecialchars($_SESSION['settings_password_error']) ?></p>
            <?php unset($_SESSION['settings_password_error']); endif; ?>

            <section class="settings-section">
                <h2 class="settings-section-title">
                    <span class="iconify" data-icon="mdi:account-outline" data-width="24" data-height="24" style="color: #FF383C;"></span>
                    Profil
                </h2>
                <form class="settings-form" method="post" action="/settings">
                    <label class="settings-label" for="settings-name">Imię</label>
                    <div class="settings-row">
                        <input type="text" id="settings-name" name="name" class="settings-input" value="<?= htmlspecialchars($userName ?? '') ?>" maxlength="255">
                        <button type="submit" class="settings-btn">Zapisz</button>
                    </div>
                </form>
            </section>

            <section class="settings-section">
                <h2 class="settings-section-title">
                    <span class="iconify" data-icon="mdi:shield-account-outline" data-width="24" data-height="24" style="color: #FF383C;"></span>
                    Bezpieczeństwo
                </h2>
                <form class="settings-form" method="post" action="/settings/changePassword">
                    <label class="settings-label" for="settings-current">Aktualne hasło</label>
                    <div class="settings-input-wrap">
                        <input type="password" id="settings-current" name="current_password" class="settings-input" placeholder="Aktualne hasło">
                    </div>

                    <label class="settings-label" for="settings-new">Nowe hasło</label>
                    <input type="password" id="settings-new" name="new_password" class="settings-input" placeholder="Min. 8 znaków">

                    <label class="settings-label" for="settings-repeat">Powtórz nowe hasło</label>
                    <input type="password" id="settings-repeat" name="new_password_repeat" class="settings-input" placeholder="Powtórz hasło">

                    <button type="submit" class="settings-btn">Zmień hasło</button>
                </form>
            </section>

            <?php if (isset($_SESSION['settings_password_success'])): ?>
            <p class="settings-message settings-message--success">Hasło zmienione.</p>
            <?php unset($_SESSION['settings_password_success']); endif; ?>
        </main>

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
            <a href="/settings" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'settings' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:cog-outline" data-width="28" data-height="28"></span>
                <span>Ustawienia</span>
            </a>
        </nav>
    </div>
</body>
</html>