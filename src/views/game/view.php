<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($game['name'] ?? 'Gra') ?> – HarcApp</title>
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
                <h1 class="dashboard-title"><?= htmlspecialchars($game['name'] ?? 'Gra') ?></h1>
                <div class="dashboard-header-actions">
                    <a href="/game" class="dashboard-header-link">
                        <span class="iconify" data-icon="mdi:arrow-left" data-width="20" data-height="20"></span>
                        <span>Wstecz</span>
                    </a>
                    <a href="/security/logout" class="dashboard-header-link">
                        <span class="iconify" data-icon="mdi:logout" data-width="20" data-height="20"></span>
                        <span>Wyloguj się</span>
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Karta z podstawowymi informacjami -->
                <div class="game-view-card">
                    <div class="game-view-header">
                        <h1 class="game-view-title">Nazwa</h1>
                        <p class="game-view-value"><?= htmlspecialchars($game['name'] ?? '') ?></p>
                    </div>

                    <?php if (!empty($game['description'])): ?>
                    <div class="game-view-row">
                        <h2 class="game-view-label">Opis</h2>
                        <p class="game-view-value"><?= nl2br(htmlspecialchars($game['description'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="game-view-row">
                        <h2 class="game-view-label">Utworzono</h2>
                        <p class="game-view-value"><?= $game['created_at'] ? date('Y-m-d H:i', strtotime($game['created_at'])) : '—' ?></p>
                    </div>

                    <?php if (!empty($game['creator_name'])): ?>
                    <div class="game-view-row">
                        <h2 class="game-view-label">Autor</h2>
                        <p class="game-view-value"><?= htmlspecialchars($game['creator_name']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($game['started_at'])): ?>
                    <div class="game-view-row">
                        <h2 class="game-view-label">Data rozpoczęcia</h2>
                        <p class="game-view-value"><?= date('Y-m-d H:i', strtotime($game['started_at'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($game['ended_at'])): ?>
                    <div class="game-view-row">
                        <h2 class="game-view-label">Data zakończenia</h2>
                        <p class="game-view-value"><?= date('Y-m-d H:i', strtotime($game['ended_at'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sekcja 2: Punkty -->
                <section class="game-create-section">
                    <h2 class="game-create-section-title">
                        <span class="iconify" data-icon="mdi:map-marker-outline" data-width="22" data-height="22" style="color: #FF383C;"></span>
                        Punkty
                    </h2>
                    <?php if (empty($points)): ?>
                        <p class="game-view-empty">Brak zdefiniowanych punktów</p>
                    <?php else: ?>
                        <?php foreach ($points as $point): ?>
                        <div class="game-view-point-card">
                            <div class="game-view-row">
                                <h3 class="game-view-label">Nazwa punktu</h3>
                                <p class="game-view-value"><?= htmlspecialchars($point['name'] ?? 'Punkt bez nazwy') ?></p>
                            </div>
                            <div class="game-view-row">
                                <h3 class="game-view-label">Punktowy</h3>
                                <p class="game-view-value"><?= htmlspecialchars($point['scorer_name'] ?? 'Brak punktowego') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>

                <!-- Sekcja 3: Zastępy i Uczestnicy -->
                <section class="game-create-section">
                    <h2 class="game-create-section-title">
                        <span class="iconify" data-icon="mdi:account-group-outline" data-width="22" data-height="22" style="color: #FF383C;"></span>
                        Zastępy i Uczestnicy
                    </h2>
                    <?php if (empty($teams)): ?>
                        <p class="game-view-empty">Brak zdefiniowanych zastępów</p>
                    <?php else: ?>
                        <?php foreach ($teams as $team): ?>
                        <div class="game-view-team-card">
                            <div class="game-view-team-header">
                                <span class="iconify" data-icon="mdi:account-circle" data-width="28" data-height="28" style="color: #4ade80;"></span>
                                <h3 class="game-view-team-name"><?= htmlspecialchars($team['name'] ?? 'Zastęp bez nazwy') ?></h3>
                                <span class="game-view-team-badge">
                                    <?php 
                                    $teamId = $team['id'];
                                    $count = isset($teamParticipants[$teamId]) ? count($teamParticipants[$teamId]) : 0;
                                    echo $count . ' ' . ($count === 1 ? 'osoba' : 'osób');
                                    ?>
                                </span>
                            </div>
                            <?php if (isset($teamParticipants[$teamId]) && !empty($teamParticipants[$teamId])): ?>
                            <ul class="game-view-participants">
                                <?php foreach ($teamParticipants[$teamId] as $participant): ?>
                                <li class="game-view-participant">
                                    <span class="iconify" data-icon="mdi:account" data-width="16" data-height="16" style="color: #B0B0B0;"></span>
                                    <span><?= htmlspecialchars($participant['user_name']) ?></span>
                                    <span class="game-view-participant-email"><?= htmlspecialchars($participant['user_email']) ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="game-view-empty-team">Brak uczestników</p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
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
