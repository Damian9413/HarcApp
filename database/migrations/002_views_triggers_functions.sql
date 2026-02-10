-- widoki, funkcje i triggery

-- widok 1 - podsumowanie gier
DROP VIEW IF EXISTS view_games_summary;

CREATE VIEW view_games_summary AS
SELECT 
    g.id AS game_id,
    g.name AS game_name,
    g.description,
    g.created_at,
    g.started_at,
    g.ended_at,
    u.id AS creator_id,
    u.name AS creator_name,
    u.email AS creator_email,
    COUNT(DISTINCT gp.id) AS points_count,
    COUNT(DISTINCT gt.id) AS teams_count,
    COUNT(DISTINCT gpart.user_id) AS participants_count,
    COUNT(DISTINCT gs.user_id) AS scorers_count
FROM games g
LEFT JOIN users u ON g.creator_id = u.id
LEFT JOIN game_points gp ON g.id = gp.game_id
LEFT JOIN game_teams gt ON g.id = gt.game_id
LEFT JOIN game_participants gpart ON g.id = gpart.game_id
LEFT JOIN game_scorers gs ON g.id = gs.game_id
GROUP BY g.id, g.name, g.description, g.created_at, g.started_at, g.ended_at, u.id, u.name, u.email
ORDER BY g.created_at DESC;

-- widok 2 - ranking zastepow
DROP VIEW IF EXISTS view_team_rankings;

CREATE VIEW view_team_rankings AS
SELECT 
    g.id AS game_id,
    g.name AS game_name,
    gt.id AS team_id,
    gt.name AS team_name,
    COALESCE(SUM(gsc.points), 0) AS total_points,
    COUNT(DISTINCT gpart.user_id) AS members_count,
    RANK() OVER (PARTITION BY g.id ORDER BY COALESCE(SUM(gsc.points), 0) DESC) AS rank_position
FROM games g
JOIN game_teams gt ON g.id = gt.game_id
LEFT JOIN game_participants gpart ON gt.id = gpart.team_id AND g.id = gpart.game_id
LEFT JOIN game_scores gsc ON gt.id = gsc.team_id AND g.id = gsc.game_id
GROUP BY g.id, g.name, gt.id, gt.name
ORDER BY g.id, total_points DESC;

-- tabela do logow
DROP TABLE IF EXISTS game_audit_log;

CREATE TABLE game_audit_log (
    id SERIAL PRIMARY KEY,
    game_id INTEGER NOT NULL,
    action VARCHAR(10) NOT NULL,
    old_name VARCHAR(255),
    new_name VARCHAR(255),
    changed_by INTEGER,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- funkcja 1 - suma punktow zastepu
DROP FUNCTION IF EXISTS get_team_total_points(INTEGER, INTEGER);

CREATE OR REPLACE FUNCTION get_team_total_points(p_game_id INTEGER, p_team_id INTEGER)
RETURNS INTEGER AS $$
DECLARE
    total INTEGER;
BEGIN
    SELECT COALESCE(SUM(points), 0) INTO total
    FROM game_scores
    WHERE game_id = p_game_id AND team_id = p_team_id;
    RETURN total;
END;
$$ LANGUAGE plpgsql;

-- funkcja 2 - zwyciezca gry
DROP FUNCTION IF EXISTS get_game_winner(INTEGER);

CREATE OR REPLACE FUNCTION get_game_winner(p_game_id INTEGER)
RETURNS VARCHAR AS $$
DECLARE
    winner_name VARCHAR(255);
BEGIN
    SELECT gt.name INTO winner_name
    FROM game_teams gt
    LEFT JOIN game_scores gsc ON gt.id = gsc.team_id AND gt.game_id = gsc.game_id
    WHERE gt.game_id = p_game_id
    GROUP BY gt.id, gt.name
    ORDER BY COALESCE(SUM(gsc.points), 0) DESC
    LIMIT 1;
    RETURN COALESCE(winner_name, 'Brak zastępów');
END;
$$ LANGUAGE plpgsql;

-- trigger - loguje zmiany w grach
DROP TRIGGER IF EXISTS game_changes_trigger ON games;
DROP FUNCTION IF EXISTS log_game_changes();

CREATE OR REPLACE FUNCTION log_game_changes()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        INSERT INTO game_audit_log (game_id, action, new_name, changed_at)
        VALUES (NEW.id, 'INSERT', NEW.name, CURRENT_TIMESTAMP);
        RETURN NEW;
    ELSIF TG_OP = 'UPDATE' THEN
        INSERT INTO game_audit_log (game_id, action, old_name, new_name, changed_at)
        VALUES (NEW.id, 'UPDATE', OLD.name, NEW.name, CURRENT_TIMESTAMP);
        RETURN NEW;
    ELSIF TG_OP = 'DELETE' THEN
        INSERT INTO game_audit_log (game_id, action, old_name, changed_at)
        VALUES (OLD.id, 'DELETE', OLD.name, CURRENT_TIMESTAMP);
        RETURN OLD;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER game_changes_trigger
AFTER INSERT OR UPDATE OR DELETE ON games
FOR EACH ROW
EXECUTE FUNCTION log_game_changes();
