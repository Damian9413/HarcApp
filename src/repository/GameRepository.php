<?php

// singleton do gier

class GameRepository
{
    private static ?GameRepository $instance = null;
    private PDO $database;

    private function __construct()
    {
        $this->database = Database::connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllGames(): array
    {
        $stmt = $this->database->query('
            SELECT g.id, g.name, g.description, g.created_at, u.name AS creator_name
            FROM games g
            LEFT JOIN users u ON g.creator_id = u.id
            ORDER BY g.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameById(int $id): ?array
    {
        $stmt = $this->database->prepare('
            SELECT g.id, g.name, g.description, g.created_at, g.started_at, g.ended_at, u.name AS creator_name
            FROM games g
            LEFT JOIN users u ON g.creator_id = u.id
            WHERE g.id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createGame(string $name, string $description, int $createdByUserId, ?string $startedAt = null, ?string $endedAt = null): int
    {
        $stmt = $this->database->prepare('
            INSERT INTO games (name, description, creator_id, started_at, ended_at)
            VALUES (:name, :description, :creator_id, :started_at, :ended_at)
        ');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':creator_id', $createdByUserId, PDO::PARAM_INT);
        $stmt->bindValue(':started_at', $startedAt, $startedAt ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':ended_at', $endedAt, $endedAt ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();
        return (int) $this->database->lastInsertId();
    }

    // transakcje
    public function beginTransaction(): void
    {
        $this->database->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
        $this->database->beginTransaction();
    }

    public function commit(): void
    {
        $this->database->commit();
    }

    public function rollback(): void
    {
        if ($this->database->inTransaction()) {
            $this->database->rollBack();
        }
    }

    // tworzy gre z punktami i zastepami w transakcji
    public function createFullGame(
        string $name,
        string $description,
        int $creatorId,
        ?string $startedAt,
        ?string $endedAt,
        array $points,
        array $teams
    ): int {
        try {
            $this->beginTransaction();

            $gameId = $this->createGame($name, $description, $creatorId, $startedAt, $endedAt);

            foreach ($points as $index => $point) {
                $this->addGamePoint($gameId, $point['name'], $point['max_points'] ?? 10, $index);
            }

            foreach ($teams as $index => $team) {
                $teamId = $this->addGameTeam($gameId, $team['name'], $index);
                
                if (!empty($team['participants'])) {
                    foreach ($team['participants'] as $userId) {
                        $this->addGameParticipant($gameId, $userId, $teamId);
                    }
                }
            }

            $this->commit();
            return $gameId;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function deleteGame(int $id): bool
    {
        $stmt = $this->database->prepare('DELETE FROM games WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function updateGame(int $id, string $name, string $description, ?string $startedAt = null, ?string $endedAt = null): bool
    {
        $stmt = $this->database->prepare('
            UPDATE games SET name = :name, description = :description, started_at = :started_at, ended_at = :ended_at
            WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':started_at', $startedAt, $startedAt ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':ended_at', $endedAt, $endedAt ? PDO::PARAM_STR : PDO::PARAM_NULL);
        return $stmt->execute();
    }

    // usuwa szczegoly gry w transakcji
    public function deleteGameDetails(int $gameId): void
    {
        try {
            $this->beginTransaction();

            $stmt = $this->database->prepare('DELETE FROM game_scorers WHERE game_id = :game_id');
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->database->prepare('DELETE FROM game_participants WHERE game_id = :game_id');
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->database->prepare('DELETE FROM game_teams WHERE game_id = :game_id');
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->database->prepare('DELETE FROM game_points WHERE game_id = :game_id');
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function getLastInsertId(): int
    {
        return (int) $this->database->lastInsertId();
    }

    public function addGamePoint(int $gameId, string $name, int $points, int $sortOrder = 0): int
    {
        $stmt = $this->database->prepare('
            INSERT INTO game_points (game_id, name, sort_order)
            VALUES (:game_id, :name, :sort_order)
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $this->database->lastInsertId();
    }

    public function addGameScorer(int $gameId, int $gamePointId, int $userId): void
    {
        $stmt = $this->database->prepare('
            INSERT INTO game_scorers (game_id, game_point_id, user_id)
            VALUES (:game_id, :game_point_id, :user_id)
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':game_point_id', $gamePointId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addGameTeam(int $gameId, string $name, int $sortOrder = 0): int
    {
        $stmt = $this->database->prepare('
            INSERT INTO game_teams (game_id, name, sort_order)
            VALUES (:game_id, :name, :sort_order)
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $this->database->lastInsertId();
    }

    public function addGameParticipant(int $gameId, int $userId, ?int $teamId = null): void
    {
        $stmt = $this->database->prepare('
            INSERT INTO game_participants (game_id, user_id, team_id)
            VALUES (:game_id, :user_id, :team_id)
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getGamePoints(int $gameId): array
    {
        $stmt = $this->database->prepare('
            SELECT gp.id, gp.name, gp.sort_order, u.id AS scorer_id, u.name AS scorer_name, u.email AS scorer_email
            FROM game_points gp
            LEFT JOIN game_scorers gs ON gp.id = gs.game_point_id
            LEFT JOIN users u ON gs.user_id = u.id
            WHERE gp.game_id = :game_id
            ORDER BY gp.sort_order ASC
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameTeams(int $gameId): array
    {
        $stmt = $this->database->prepare('
            SELECT id, name, sort_order FROM game_teams WHERE game_id = :game_id ORDER BY sort_order ASC
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameParticipants(int $gameId): array
    {
        $stmt = $this->database->prepare('
            SELECT gp.id, gp.team_id, u.id AS user_id, u.name AS user_name, u.email AS user_email, u.role AS user_role
            FROM game_participants gp
            JOIN users u ON gp.user_id = u.id
            WHERE gp.game_id = :game_id
            ORDER BY gp.team_id ASC, u.name ASC
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // gry usera jako uczestnik
    public function getGamesByParticipant(int $userId): array
    {
        $stmt = $this->database->prepare('
            SELECT DISTINCT g.id, g.name, g.description, g.created_at, g.started_at, g.ended_at, gt.id AS team_id, gt.name AS team_name
            FROM games g
            JOIN game_participants gp ON g.id = gp.game_id
            LEFT JOIN game_teams gt ON gp.team_id = gt.id
            WHERE gp.user_id = :user_id
            ORDER BY g.created_at DESC
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // gry usera jako punktowy
    public function getGamesByScorer(int $userId): array
    {
        $stmt = $this->database->prepare('
            SELECT DISTINCT g.id, g.name, g.description, g.created_at, g.started_at, g.ended_at
            FROM games g
            JOIN game_scorers gs ON g.id = gs.game_id
            WHERE gs.user_id = :user_id
            ORDER BY g.created_at DESC
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGamePointsByScorer(int $gameId, int $scorerId): array
    {
        $stmt = $this->database->prepare('
            SELECT gp.id, gp.name, gp.sort_order
            FROM game_points gp
            JOIN game_scorers gs ON gp.id = gs.game_point_id
            WHERE gp.game_id = :game_id AND gs.user_id = :user_id
            ORDER BY gp.sort_order ASC
        ');
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $scorerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
