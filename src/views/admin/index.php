<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Użytkownicy – HarcApp</title>
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
                <a href="/admin" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'admin' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:account-group-outline" data-width="48" data-height="48"></span>
                    <span>Użytkownicy</span>
                </a>
                <a href="/settings" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'settings' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:cog-outline" data-width="48" data-height="48"></span>
                    <span>Ustawienia</span>
                </a>
            </nav>

            <div class="dashboard-user">
                <span class="iconify dashboard-user-icon" data-icon="mdi:account-circle" data-width="48" data-height="48" style="color: #FF383C;"></span>
                <div class="dashboard-user-info">
                    <span class="dashboard-user-role"><?= htmlspecialchars($userRoleLabel ?? $userRole ?? 'Użytkownik') ?></span>
                    <span class="dashboard-user-email"><?= htmlspecialchars($userEmail ?? '') ?></span>
                </div>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-header">
                <h1 class="dashboard-title">Użytkownicy</h1>
                <a href="/security/logout" class="dashboard-header-link">
                    <span class="iconify" data-icon="mdi:logout" data-width="24" data-height="24" style="color: #fff;"></span>
                    <span>Wyloguj się</span>
                </a>
            </header>

            <div class="admin-content">
                <!-- Góra: zmiana roli (lista od uczestnika do admina) -->
                <section class="admin-section">
                    <h2 class="admin-section-title">Zmiana roli</h2>
                    <?php if (empty($approvedUsers)): ?>
                        <p class="admin-empty">Brak zatwierdzonych użytkowników.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Imię</th>
                                    <th>Rola</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $roles = [
                                    'uczestnik' => 'Uczestnik',
                                    'punktowy'   => 'Punktowy',
                                    'twórca'     => 'Twórca',
                                    'admin'      => 'Administrator',
                                ];
                                foreach ($approvedUsers as $user): ?>
                                <tr>
                                    <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                                    <td data-label="Imię"><?= htmlspecialchars($user['name']) ?></td>
                                    <td data-label="Rola">
                                        <form class="admin-role-form" method="post" action="/admin/changeRole">
                                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                            <select name="role" class="admin-role-select" aria-label="Rola">
                                                <?php foreach ($roles as $value => $label): ?>
                                                <option value="<?= htmlspecialchars($value) ?>"<?= ($user['role'] ?? '') === $value ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="admin-role-submit">Zapisz</button>
                                        </form>
                                    </td>
                                    <td data-label="Akcja">
                                        <?php if ((int) $user['id'] !== (int) ($_SESSION['user_id'] ?? 0)): ?>
                                        <a href="/admin/delete?id=<?= (int) $user['id'] ?>" class="admin-link-delete">Usuń użytkownika</a>
                                        <?php else: ?>
                                        —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

                <!-- Dół: zatwierdzanie użytkowników -->
                <section class="admin-section">
                    <h2 class="admin-section-title">Zatwierdzanie użytkowników</h2>
                    <?php if (empty($usersToApprove)): ?>
                        <p class="admin-empty">Brak użytkowników do zatwierdzenia.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Imię</th>
                                    <th>Rola</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usersToApprove as $user): ?>
                                <tr>
                                    <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                                    <td data-label="Imię"><?= htmlspecialchars($user['name']) ?></td>
                                    <td data-label="Rola"><?= htmlspecialchars($user['role']) ?></td>
                                    <td data-label="Akcja">
                                        <a href="/admin/approve?id=<?= (int) $user['id'] ?>" class="admin-link-accent">Zatwierdź</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
            </div>
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
            <a href="/admin" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'admin' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:account-group-outline" data-width="28" data-height="28"></span>
                <span>Użytkownicy</span>
            </a>
            <a href="/settings" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'settings' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:cog-outline" data-width="28" data-height="28"></span>
                <span>Ustawienia</span>
            </a>
        </nav>
    </div>
</body>
</html>
