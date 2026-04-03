<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gry – HarcApp</title>
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
                <a href="/home" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'dashboard' ? ' dashboard-nav-item--active' : '' ?>">
                    <span class="iconify" data-icon="mdi:view-dashboard" data-width="48" data-height="48"></span>
                    <span>Dashboard</span>
                </a>
                <a href="/game" class="dashboard-nav-item<?= ($sidebarActive ?? '') === 'games' ? ' dashboard-nav-item--active' : '' ?>">
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
                    <span class="dashboard-user-role"><?= htmlspecialchars($userRoleLabel ?? $userRole ?? 'Użytkownik') ?></span>
                    <span class="dashboard-user-email"><?= htmlspecialchars($userEmail ?? '') ?></span>
                </div>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-header">
                <h1 class="dashboard-title">Panel gier</h1>
                <div class="dashboard-header-actions">
                    <?php if (isset($userRole) && ($userRole === 'admin' || $userRole === 'tworca')): ?>
                    <a href="/game/create" class="dashboard-header-link games-btn-new">
                        <span class="iconify" data-icon="mdi:plus" data-width="20" data-height="20"></span>
                        <span>Nowa gra</span>
                    </a>
                    <?php endif; ?>
                    <a href="/security/logout" class="dashboard-header-link">
                        <span class="iconify" data-icon="mdi:logout" data-width="20" data-height="20"></span>
                        <span>Wyloguj się</span>
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <section class="admin-section">
                    <h2 class="admin-section-title">Wszystkie gry</h2>
                    <?php if (empty($games)): ?>
                        <?php if (isset($userRole) && ($userRole === 'admin' || $userRole === 'tworca')): ?>
                        <p class="admin-empty">Brak gier. <a href="/game/create" class="admin-link-accent">Stwórz pierwszą grę</a>.</p>
                        <?php else: ?>
                        <p class="admin-empty">Nie jesteś przypisany do żadnej gry.</p>
                        <?php endif; ?>
                        <div class="games-pagination">
                            <span class="games-pagination-info">Wyświetlono 0 z 0 gier</span>
                            <div class="games-pagination-btns">
                                <span class="games-pagination-btn games-pagination-btn--disabled">Poprzednia</span>
                                <span class="games-pagination-btn games-pagination-btn--disabled">Następna</span>
                            </div>
                        </div>
                    <?php else:
                        $total = count($games);
                    ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nazwa</th>
                                    <?php if (isset($userRole) && ($userRole === 'uczestnik')): ?>
                                    <th>Zastęp</th>
                                    <?php else: ?>
                                    <th>Opis</th>
                                    <?php endif; ?>
                                    <th>Utworzono</th>
                                    <?php if (isset($userRole) && ($userRole === 'admin' || $userRole === 'tworca')): ?>
                                    <th>Autor</th>
                                    <?php endif; ?>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($games as $game): ?>
                                <tr>
                                    <td data-label="Nazwa"><?= htmlspecialchars($game['name']) ?></td>
                                    <?php if (isset($userRole) && ($userRole === 'uczestnik')): ?>
                                    <td data-label="Zastęp"><?= htmlspecialchars($game['team_name'] ?? 'Brak zastępu') ?></td>
                                    <?php else: ?>
                                    <td data-label="Opis"><?= htmlspecialchars($game['description'] ?? '') ?></td>
                                    <?php endif; ?>
                                    <td data-label="Utworzono"><?= $game['created_at'] ? date('Y-m-d H:i', strtotime($game['created_at'])) : '' ?></td>
                                    <?php if (isset($userRole) && ($userRole === 'admin' || $userRole === 'tworca')): ?>
                                    <td data-label="Autor"><?= htmlspecialchars($game['creator_name'] ?? '—') ?></td>
                                    <?php endif; ?>
                                    <td data-label="Akcje">
                                        <div class="games-actions">
                                            <a href="/game/view?id=<?= (int) $game['id'] ?>" class="games-action-btn games-action-btn--primary">Zobacz</a>
                                            <?php if (isset($userRole) && ($userRole === 'admin' || $userRole === 'tworca')): ?>
                                            <a href="/game/edit?id=<?= (int) $game['id'] ?>" class="games-action-btn games-action-btn--secondary">Edytuj</a>
                                            <?php endif; ?>
                                            <?php if (isset($userRole) && $userRole === 'admin'): ?>
                                            <a href="/game/delete?id=<?= (int) $game['id'] ?>" class="games-action-btn games-action-btn--danger">Usuń</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="games-pagination">
                            <span class="games-pagination-info">Wyświetlono <?= $total ?> z <?= $total ?> gier</span>
                            <div class="games-pagination-btns">
                                <span class="games-pagination-btn games-pagination-btn--disabled">Poprzednia</span>
                                <span class="games-pagination-btn games-pagination-btn--disabled">Następna</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>

        <nav class="dashboard-footer-nav" aria-label="Nawigacja główna">
            <a href="/home" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'dashboard' ? ' dashboard-footer-nav-item--active' : '' ?>">
                <span class="iconify" data-icon="mdi:view-dashboard" data-width="28" data-height="28"></span>
                <span>Dashboard</span>
            </a>
            <a href="/game" class="dashboard-footer-nav-item<?= ($sidebarActive ?? '') === 'games' ? ' dashboard-footer-nav-item--active' : '' ?>">
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