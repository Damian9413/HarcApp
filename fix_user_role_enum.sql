-- Napraw enum user_role aby obsługiwał polskie znaki
-- Wykonaj ten skrypt w pgAdmin lub przez psql

-- 1. Najpierw sprawdź aktualne wartości
-- SELECT enum_range(NULL::user_role);

-- 2. Zmień typ kolumny na VARCHAR tymczasowo
ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20);

-- 3. Usuń stary enum (jeśli istnieje)
DROP TYPE IF EXISTS user_role CASCADE;

-- 4. Utwórz nowy enum z polskimi znakami (bez użycia typu enum, używamy CHECK constraint)
-- Alternatywnie: pozostaw jako VARCHAR z CHECK constraint
ALTER TABLE users 
  DROP CONSTRAINT IF EXISTS users_role_check;

ALTER TABLE users
  ADD CONSTRAINT users_role_check 
  CHECK (role IN ('admin', 'twórca', 'punktowy', 'uczestnik'));

-- 5. Opcjonalnie: zaktualizuj istniejące wartości jeśli są w starym formacie
-- UPDATE users SET role = 'twórca' WHERE role = 'creator';
-- UPDATE users SET role = 'punktowy' WHERE role = 'scorer';
-- UPDATE users SET role = 'uczestnik' WHERE role = 'participant';

-- Sprawdź wynik
SELECT id, email, role FROM users;
