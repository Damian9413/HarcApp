<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edycja gry – HarcApp</title>
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
                <h1 class="dashboard-title">Edycja gry</h1>
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

            <?php if (!empty($_SESSION['games_create_error'])): ?>
                <p class="settings-message settings-message--error"><?= htmlspecialchars($_SESSION['games_create_error']) ?></p>
                <?php unset($_SESSION['games_create_error']); ?>
            <?php endif; ?>

            <form class="game-create-form" method="post" action="/game/editPost">
                <input type="hidden" name="game_id" value="<?= (int) ($game['id'] ?? 0) ?>">
                <!-- Sekcja 1: Szczegóły gry -->
                <section class="game-create-section">
                    <h2 class="game-create-section-title">
                        <span class="iconify" data-icon="mdi:information-outline" data-width="22" data-height="22" style="color: #FF383C;"></span>
                        Szczegóły gry
                    </h2>
                    <label class="game-create-label" for="game-name">Nazwa gry</label>
                    <input type="text" id="game-name" name="name" class="game-create-input" placeholder="Wpisz nazwę gry..." required maxlength="255" value="<?= htmlspecialchars($game['name'] ?? '') ?>">

                    <label class="game-create-label" for="game-desc">Opis (opcjonalnie)</label>
                    <textarea id="game-desc" name="description" class="game-create-input game-create-textarea" rows="4" placeholder="Krótki opis gry dla uczestników..."><?= htmlspecialchars($game['description'] ?? '') ?></textarea>

                    <div class="game-create-row">
                        <div class="game-create-field">
                            <label class="game-create-label" for="game-start">Data i godzina rozpoczęcia</label>
                            <input type="datetime-local" id="game-start" name="start_datetime" class="game-create-input game-create-input-datetime" value="<?= !empty($game['started_at']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($game['started_at']))) : '' ?>">
                        </div>
                        <div class="game-create-field">
                            <label class="game-create-label" for="game-end">Data i godzina zakończenia</label>
                            <input type="datetime-local" id="game-end" name="end_datetime" class="game-create-input game-create-input-datetime" value="<?= !empty($game['ended_at']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($game['ended_at']))) : '' ?>">
                        </div>
                    </div>
                </section>

                <!-- Sekcja 2: Punkty (wybór punktowego + wartość) -->
                <section class="game-create-section game-create-section-points" data-punktowi="<?= htmlspecialchars(json_encode($punktowi ?? []), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="game-create-section-head">
                        <h2 class="game-create-section-title">
                            <span class="iconify" data-icon="mdi:map-marker-outline" data-width="22" data-height="22" style="color: #FF383C;"></span>
                            Punkty
                        </h2>
                        <button type="button" class="game-create-btn-add game-create-btn-add-point">
                            <span class="iconify" data-icon="mdi:plus" data-width="20" data-height="20"></span>
                            Dodaj punkt
                        </button>
                    </div>
                    <div class="game-create-checkpoints-list">
                        <div class="game-create-checkpoint-header">
                            <span class="game-create-checkpoint-label">Nazwa punktu</span>
                            <span class="game-create-checkpoint-label">Punktowy</span>
                            <span class="game-create-checkpoint-label">Punkty</span>
                            <span class="game-create-checkpoint-label"></span>
                        </div>
                        <?php if (!empty($points)): ?>
                            <?php foreach ($points as $idx => $point): ?>
                            <div class="game-create-checkpoint">
                                <input type="text" class="game-create-input game-create-input-inline" name="points[<?= $idx ?>][name]" placeholder="np. Strażnica" value="<?= htmlspecialchars($point['name'] ?? '') ?>">
                                <select class="game-create-input game-create-input-inline game-create-select-punktowy" name="points[<?= $idx ?>][user_id]" aria-label="Wybierz punktowego">
                                    <option value="">-- Wybierz punktowego --</option>
                                    <?php foreach ($punktowi ?? [] as $p): ?>
                                    <option value="<?= (int) $p['id'] ?>" <?= isset($point['scorer_id']) && (int)$point['scorer_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" class="game-create-input game-create-input-inline game-create-input-points" value="10" min="0" name="points[<?= $idx ?>][points]">
                                <span class="iconify game-create-icon-trash game-create-remove-point" data-icon="mdi:delete-outline" data-width="20" data-height="20" title="Usuń"></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="game-create-checkpoint">
                            <input type="text" class="game-create-input game-create-input-inline" name="points[0][name]" placeholder="np. Strażnica">
                            <select class="game-create-input game-create-input-inline game-create-select-punktowy" name="points[0][user_id]" aria-label="Wybierz punktowego">
                                <option value="">-- Wybierz punktowego --</option>
                                <?php foreach ($punktowi ?? [] as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" class="game-create-input game-create-input-inline game-create-input-points" value="10" min="0" name="points[0][points]">
                            <span class="iconify game-create-icon-trash game-create-remove-point" data-icon="mdi:delete-outline" data-width="20" data-height="20" title="Usuń"></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Sekcja 3: Zastępy i Uczestnicy -->
                <section class="game-create-section game-create-section-teams">
                    <div class="game-create-section-head">
                        <h2 class="game-create-section-title">
                            <span class="iconify" data-icon="mdi:account-group-outline" data-width="22" data-height="22" style="color: #FF383C;"></span>
                            Zastępy i Uczestnicy
                        </h2>
                        <button type="button" class="game-create-btn-add game-create-btn-add-team">
                            <span class="iconify" data-icon="mdi:plus" data-width="20" data-height="20"></span>
                            Utwórz zastęp
                        </button>
                    </div>
                    <div class="game-create-teams" id="teams-container">
                        <?php if (!empty($teams)): ?>
                            <?php foreach ($teams as $tidx => $team): ?>
                            <div class="game-create-team-card" data-team-index="<?= $tidx ?>">
                                <div class="game-create-team-header">
                                    <span class="iconify game-create-team-icon" data-icon="mdi:account-circle" data-width="28" data-height="28" style="color: #4ade80;"></span>
                                    <input type="text" class="game-create-team-name" name="teams[<?= $tidx ?>][name]" value="<?= htmlspecialchars($team['name'] ?? '') ?>" placeholder="Nazwa zastępu">
                                    <span class="game-create-team-badge game-create-team-count">
                                        <?php 
                                        $teamId = $team['id'];
                                        $count = isset($teamParticipants[$teamId]) ? count($teamParticipants[$teamId]) : 0;
                                        echo $count . ' ' . ($count === 1 ? 'osoba' : 'osób');
                                        ?>
                                    </span>
                                </div>
                                <ul class="game-create-team-list" data-user-list>
                                    <?php if (isset($teamParticipants[$teamId])): ?>
                                        <?php foreach ($teamParticipants[$teamId] as $participant): ?>
                                        <li>
                                            <span class="game-create-participant-name"><?= htmlspecialchars($participant['user_name']) ?> (<?= htmlspecialchars($participant['user_email']) ?>)</span>
                                            <span class="iconify game-create-remove-participant" data-icon="mdi:close" data-width="16" data-height="16" title="Usuń"></span>
                                            <input type="hidden" name="teams[<?= $tidx ?>][user_ids][]" value="<?= (int) $participant['user_id'] ?>">
                                        </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                                <select class="game-create-user-select" data-user-select aria-label="Wybierz uczestnika">
                                    <option value="">-- Wybierz użytkownika --</option>
                                    <?php foreach ($users ?? [] as $u): ?>
                                    <option value="<?= (int) $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="game-create-btn-add-inline" data-add-participant>+ Dodaj uczestnika</button>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="game-create-team-card" data-team-index="0">
                            <div class="game-create-team-header">
                                <span class="iconify game-create-team-icon" data-icon="mdi:account-circle" data-width="28" data-height="28" style="color: #4ade80;"></span>
                                <input type="text" class="game-create-team-name" name="teams[0][name]" value="" placeholder="Nazwa zastępu">
                                <span class="game-create-team-badge game-create-team-count">0 osób</span>
                            </div>
                            <ul class="game-create-team-list" data-user-list></ul>
                            <select class="game-create-user-select" data-user-select aria-label="Wybierz uczestnika">
                                <option value="">-- Wybierz użytkownika --</option>
                                <?php foreach ($users ?? [] as $u): ?>
                                <option value="<?= (int) $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="game-create-btn-add-inline" data-add-participant>+ Dodaj uczestnika</button>
                        </div>
                        <?php endif; ?>
                        <div class="game-create-team-card game-create-team-card--empty" id="team-card-empty">
                            <span class="iconify" data-icon="mdi:account-plus-outline" data-width="48" data-height="48"></span>
                            <span>Nowy zastęp</span>
                        </div>
                    </div>
                    <!-- Szablon karty zastępu do klonowania w JS -->
                    <template id="team-card-template">
                        <div class="game-create-team-card" data-team-index="">
                            <div class="game-create-team-header">
                                <span class="iconify game-create-team-icon" data-icon="mdi:account-circle" data-width="28" data-height="28" style="color: #4ade80;"></span>
                                <input type="text" class="game-create-team-name" name="teams[__INDEX__][name]" value="" placeholder="Nazwa zastępu">
                                <span class="game-create-team-badge game-create-team-count">0 osób</span>
                            </div>
                            <ul class="game-create-team-list" data-user-list></ul>
                            <select class="game-create-user-select" data-user-select aria-label="Wybierz uczestnika">
                                <option value="">-- Wybierz użytkownika --</option>
                                <?php foreach ($users ?? [] as $u): ?>
                                <option value="<?= (int) $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="game-create-btn-add-inline" data-add-participant>+ Dodaj uczestnika</button>
                        </div>
                    </template>
                </section>

                <div class="game-create-footer">
                    <button type="submit" class="game-create-submit">
                        <span>Zapisz zmiany</span>
                        <span class="iconify" data-icon="mdi:content-save" data-width="20" data-height="20"></span>
                    </button>
                    <p class="game-create-hint">Zapisz zmiany, aby zaktualizować grę.</p>
                </div>
            </form>
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

    <script src="/js/game-create.js"></script>
    <script src="/js/game-date-validation.js"></script>
</body>
</html>