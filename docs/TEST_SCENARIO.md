# Scenariusz testowy - HarcApp

## Wymagania wstępne

1. Docker Desktop uruchomiony
2. Aplikacja uruchomiona: `docker-compose up -d`
3. Baza danych zainicjalizowana (migracje wykonane)
4. Przeglądarka z otwartymi DevTools (F12)

## Test 1: Rejestracja nowego użytkownika

### Kroki:
1. Otwórz http://localhost:8080
2. Kliknij "Zarejestruj się!"
3. Wpisz email: `test@test.pl`
4. Obserwuj komunikat pod polem (Fetch API sprawdza dostępność)
5. Wpisz hasło: `test1234`
6. Powtórz hasło: `test1234`
7. Wpisz imię: `Tester`
8. Kliknij "Zarejestruj się"

### Oczekiwany wynik:
- ✅ Komunikat "Konto utworzone. Oczekuje na akceptację administratora."
- ✅ W DevTools → Network widać żądanie POST do `/security/checkEmail`

---

## Test 2: Logowanie jako administrator

### Kroki:
1. Otwórz http://localhost:8080
2. Wpisz email: `admin@harcapp.pl`
3. Wpisz hasło: `admin123`
4. Kliknij "Zaloguj się"

### Oczekiwany wynik:
- ✅ Przekierowanie do dashboardu
- ✅ Widoczne: "Moje gry", "Stwórz Grę", "Zarządzaj użytkownikami", "Ustawienia"

---

## Test 3: Zarządzanie użytkownikami (admin)

### Kroki:
1. Zaloguj się jako admin
2. Kliknij "Zarządzaj użytkownikami"
3. Znajdź użytkownika `test@test.pl`
4. Zmień jego rolę na "Twórca"
5. Kliknij "Zatwierdź"

### Oczekiwany wynik:
- ✅ Komunikat o pomyślnej zmianie roli
- ✅ Użytkownik widoczny z rolą "Twórca"

---

## Test 4: Tworzenie gry

### Kroki:
1. Zaloguj się jako admin lub twórca
2. Kliknij "Stwórz Grę"
3. Wypełnij formularz:
   - Nazwa: "Wielka Gra Terenowa"
   - Opis: "Gra testowa"
   - Data rozpoczęcia: jutrzejsza data
   - Data zakończenia: pojutrze
4. Dodaj punkt kontrolny:
   - Nazwa: "Punkt 1"
   - Max punktów: 10
5. Dodaj zespół:
   - Nazwa: "Zastęp Wilków"
6. Kliknij "Zapisz grę"

### Oczekiwany wynik:
- ✅ Przekierowanie do listy gier
- ✅ Nowa gra widoczna na liście

---

## Test 5: Edycja gry

### Kroki:
1. Na liście gier znajdź utworzoną grę
2. Kliknij "Edytuj" (pomarańczowy przycisk)
3. Zmień nazwę na "Wielka Gra Terenowa 2024"
4. Dodaj kolejny punkt kontrolny
5. Kliknij "Zapisz zmiany"

### Oczekiwany wynik:
- ✅ Nazwa gry zaktualizowana
- ✅ Nowy punkt widoczny

---

## Test 6: Podgląd gry

### Kroki:
1. Na liście gier kliknij "Zobacz" (niebieski przycisk)
2. Sprawdź wyświetlane informacje

### Oczekiwany wynik:
- ✅ Widoczne: nazwa, opis, daty, autor
- ✅ Widoczne: lista punktów kontrolnych
- ✅ Widoczne: lista zespołów

---

## Test 7: Usuwanie gry

### Kroki:
1. Na liście gier kliknij "Usuń" (czerwony przycisk)
2. Potwierdź usunięcie

### Oczekiwany wynik:
- ✅ Gra usunięta z listy
- ✅ Wpis w `game_audit_log` (sprawdź w pgAdmin)

---

## Test 8: Zmiana hasła

### Kroki:
1. Kliknij "Ustawienia" w menu
2. Wpisz aktualne hasło
3. Wpisz nowe hasło (min. 8 znaków)
4. Powtórz nowe hasło
5. Kliknij "Zmień hasło"

### Oczekiwany wynik:
- ✅ Komunikat o pomyślnej zmianie
- ✅ Możliwość zalogowania nowym hasłem

---

## Test 9: Kontrola dostępu (błąd 403)

### Kroki:
1. Zaloguj się jako zwykły uczestnik (nie admin)
2. Spróbuj wejść na http://localhost:8080/admin

### Oczekiwany wynik:
- ✅ Przekierowanie do dashboardu lub błąd 403
- ✅ Brak dostępu do panelu administracyjnego

---

## Test 10: Wylogowanie

### Kroki:
1. Kliknij ikonę wylogowania (drzwi) w prawym górnym rogu
2. Sprawdź sesję

### Oczekiwany wynik:
- ✅ Przekierowanie do strony logowania
- ✅ Brak dostępu do chronionych zasobów

---

## Test 11: Widok mobilny

### Kroki:
1. Otwórz DevTools (F12)
2. Włącz tryb responsywny (Ctrl+Shift+M)
3. Ustaw szerokość na 375px (iPhone)
4. Przetestuj nawigację

### Oczekiwany wynik:
- ✅ Dolny pasek nawigacji widoczny
- ✅ Formularze czytelne
- ✅ Tabele responsywne

---

## Test 12: Sprawdzenie triggera (pgAdmin)

### Kroki:
1. Otwórz pgAdmin (http://localhost:5050)
2. Wykonaj zapytanie: `SELECT * FROM game_audit_log ORDER BY changed_at DESC;`

### Oczekiwany wynik:
- ✅ Wpisy INSERT/UPDATE/DELETE dla operacji na grach

---

## Test 13: Sprawdzenie widoków SQL (pgAdmin)

### Kroki:
1. W pgAdmin wykonaj:
   ```sql
   SELECT * FROM view_games_summary;
   SELECT * FROM view_team_rankings;
   ```

### Oczekiwany wynik:
- ✅ Dane zagregowane z wielu tabel
- ✅ Ranking zespołów z sumą punktów

---

## Test 14: Sprawdzenie funkcji SQL (pgAdmin)

### Kroki:
1. W pgAdmin wykonaj:
   ```sql
   SELECT get_team_total_points(1, 1);
   SELECT get_game_winner(1);
   ```

### Oczekiwany wynik:
- ✅ Zwrócona suma punktów zespołu
- ✅ Zwrócona nazwa zwycięzcy

---

## Podsumowanie testów

| Test | Funkcjonalność | Status |
|------|----------------|--------|
| 1 | Rejestracja + Fetch API | ⬜ |
| 2 | Logowanie | ⬜ |
| 3 | Zarządzanie użytkownikami | ⬜ |
| 4 | Tworzenie gry | ⬜ |
| 5 | Edycja gry | ⬜ |
| 6 | Podgląd gry | ⬜ |
| 7 | Usuwanie gry | ⬜ |
| 8 | Zmiana hasła | ⬜ |
| 9 | Kontrola dostępu | ⬜ |
| 10 | Wylogowanie | ⬜ |
| 11 | Widok mobilny | ⬜ |
| 12 | Trigger SQL | ⬜ |
| 13 | Widoki SQL | ⬜ |
| 14 | Funkcje SQL | ⬜ |

✅ = Zaliczony | ❌ = Niezaliczony | ⬜ = Do wykonania
