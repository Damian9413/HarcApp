<?php

require_once __DIR__ . '/../repository/GameRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class GameController
{
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }
        
        $userId = (int) $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        $userEmail = $_SESSION['user_email'] ?? '';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $sidebarActive = 'games';

        $repo = GameRepository::getInstance();
        
        // rozne gry dla roznych rol
        if ($userRole === 'admin' || $userRole === 'tworca') {
            $games = $repo->getAllGames();
        } elseif ($userRole === 'punktowy') {
            $games = $repo->getGamesByScorer($userId);
        } else {
            $games = $repo->getGamesByParticipant($userId);
        }

        require __DIR__ . '/../views/game/index.php';
    }

    public function view(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        $id = (int) ($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            header('Location: /game');
            exit;
        }

        $repo = GameRepository::getInstance();
        $game = $repo->getGameById($id);
        
        if (!$game) {
            header('Location: /game');
            exit;
        }

        // sprawdz dostep
        if ($userRole === 'uczestnik') {
            $userGames = $repo->getGamesByParticipant($userId);
            $hasAccess = false;
            foreach ($userGames as $g) {
                if ((int)$g['id'] === $id) {
                    $hasAccess = true;
                    break;
                }
            }
            if (!$hasAccess) {
                header('Location: /game');
                exit;
            }
        } elseif ($userRole === 'punktowy') {
            $userGames = $repo->getGamesByScorer($userId);
            $hasAccess = false;
            foreach ($userGames as $g) {
                if ((int)$g['id'] === $id) {
                    $hasAccess = true;
                    break;
                }
            }
            if (!$hasAccess) {
                header('Location: /game');
                exit;
            }
        }

        $points = $repo->getGamePoints($id);
        $teams = $repo->getGameTeams($id);
        $participants = $repo->getGameParticipants($id);

        // grupuj po zastepach
        $teamParticipants = [];
        foreach ($participants as $p) {
            $teamId = $p['team_id'] ?? 0;
            if (!isset($teamParticipants[$teamId])) {
                $teamParticipants[$teamId] = [];
            }
            $teamParticipants[$teamId][] = $p;
        }

        $userEmail = $_SESSION['user_email'] ?? '';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $sidebarActive = 'games';

        require __DIR__ . '/../views/game/view.php';
    }

    public function edit(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /game');
            exit;
        }

        $repo = GameRepository::getInstance();
        $game = $repo->getGameById($id);
        if (!$game) {
            header('Location: /game');
            exit;
        }

        $points = $repo->getGamePoints($id);
        $teams = $repo->getGameTeams($id);
        $participants = $repo->getGameParticipants($id);

        $teamParticipants = [];
        foreach ($participants as $p) {
            $teamId = $p['team_id'] ?? 0;
            if (!isset($teamParticipants[$teamId])) {
                $teamParticipants[$teamId] = [];
            }
            $teamParticipants[$teamId][] = $p;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        $userEmail = $_SESSION['user_email'] ?? '';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $sidebarActive = 'games';

        $users = UserRepository::getInstance()->getApprovedUsers();
        $punktowi = array_values(array_filter($users, fn($u) => ($u['role'] ?? '') === 'punktowy'));

        require __DIR__ . '/../views/game/edit.php';
    }

    public function editPost(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $gameId = (int) ($_POST['game_id'] ?? 0);
        if ($gameId <= 0) {
            header('Location: /game');
            exit;
        }

        $repo = GameRepository::getInstance();
        
        // usun stare dane
        $repo->deleteGameDetails($gameId);

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDatetime = trim($_POST['start_datetime'] ?? '');
        $endDatetime = trim($_POST['end_datetime'] ?? '');

        // format datetime
        $startedAt = null;
        $endedAt = null;
        if ($startDatetime) {
            $startedAt = str_replace('T', ' ', $startDatetime) . ':00';
        }
        if ($endDatetime) {
            $endedAt = str_replace('T', ' ', $endDatetime) . ':00';
        }

        $repo->updateGame($gameId, $name, $description, $startedAt, $endedAt);

        // punkty
        $points = $_POST['points'] ?? [];
        $sortOrder = 0;
        foreach ($points as $row) {
            $punktowyId = (int) ($row['user_id'] ?? 0);
            if ($punktowyId <= 0) continue;
            $pointValue = (int) ($row['points'] ?? 10);
            $pointName = trim($row['name'] ?? '');
            if ($pointName === '') $pointName = 'Punkt ' . ($sortOrder + 1);
            $pointId = $repo->addGamePoint($gameId, $pointName, $pointValue, $sortOrder);
            $repo->addGameScorer($gameId, $pointId, $punktowyId);
            $sortOrder++;
        }

        // zastepy
        $teams = $_POST['teams'] ?? [];
        $teamSortOrder = 0;
        foreach ($teams as $teamRow) {
            $teamName = trim($teamRow['name'] ?? '');
            if ($teamName === '') $teamName = 'Zastęp ' . ($teamSortOrder + 1);
            $teamId = $repo->addGameTeam($gameId, $teamName, $teamSortOrder);
            $userIds = $teamRow['user_ids'] ?? [];
            foreach ($userIds as $uid) {
                $uid = (int) $uid;
                if ($uid > 0) $repo->addGameParticipant($gameId, $uid, $teamId);
            }
            $teamSortOrder++;
        }

        header('Location: /game/view?id=' . $gameId);
        exit;
    }

    public function delete(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        if ($userRole !== 'admin') {
            header('Location: /game');
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /game');
            exit;
        }

        GameRepository::getInstance()->deleteGame($id);
        header('Location: /game');
        exit;
    }

    public function create(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        $userEmail = $_SESSION['user_email'] ?? '';
        $userRoleLabel = $this->getRoleLabel($userRole);
        $sidebarActive = 'games';

        $users = UserRepository::getInstance()->getApprovedUsers();
        $punktowi = array_values(array_filter($users, fn($u) => ($u['role'] ?? '') === 'punktowy'));

        require __DIR__ . '/../views/game/create.php';
    }

    public function createPost(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /security/login');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDatetime = trim($_POST['start_datetime'] ?? '');
        $endDatetime = trim($_POST['end_datetime'] ?? '');

        if ($name === '') {
            $_SESSION['games_create_error'] = 'Nazwa gry jest wymagana.';
            header('Location: /game/create');
            exit;
        }

        $repo = GameRepository::getInstance();

        $startedAt = null;
        if ($startDatetime !== '') {
            $startedAt = str_replace('T', ' ', $startDatetime) . ':00';
        }
        $endedAt = null;
        if ($endDatetime !== '') {
            $endedAt = str_replace('T', ' ', $endDatetime) . ':00';
        }
        $gameId = $repo->createGame($name, $description, $userId, $startedAt, $endedAt);

        // punkty
        $points = $_POST['points'] ?? [];
        $sortOrder = 0;
        foreach ($points as $row) {
            $punktowyId = (int) ($row['user_id'] ?? 0);
            if ($punktowyId <= 0) continue;
            $pointValue = (int) ($row['points'] ?? 10);
            $pointName = trim($row['name'] ?? '');
            if ($pointName === '') $pointName = 'Punkt ' . ($sortOrder + 1);
            $pointId = $repo->addGamePoint($gameId, $pointName, $pointValue, $sortOrder);
            $repo->addGameScorer($gameId, $pointId, $punktowyId);
            $sortOrder++;
        }

        // zastepy
        $teams = $_POST['teams'] ?? [];
        $teamSortOrder = 0;
        foreach ($teams as $teamRow) {
            $teamName = trim($teamRow['name'] ?? '');
            if ($teamName === '') $teamName = 'Zastęp ' . ($teamSortOrder + 1);
            $teamId = $repo->addGameTeam($gameId, $teamName, $teamSortOrder);
            $userIds = $teamRow['user_ids'] ?? [];
            foreach ($userIds as $uid) {
                $uid = (int) $uid;
                if ($uid > 0) $repo->addGameParticipant($gameId, $uid, $teamId);
            }
            $teamSortOrder++;
        }

        header('Location: /game/view?id=' . $gameId);
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
