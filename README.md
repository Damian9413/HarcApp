# HarcApp - Aplikacja do punktacji gier harcerskich

Aplikacja webowa do zarządzania grami harcerskimi, zespołami i punktacją. Projekt zaliczeniowy z przedmiotu "Wstęp do Programowania Aplikacji Internetowych" (WdPAI).

## Spis treści

- [Technologie](#technologie)
- [Wymagania](#wymagania)
- [Instalacja i uruchomienie](#instalacja-i-uruchomienie)
- [Struktura projektu](#struktura-projektu)
- [Architektura](#architektura)
- [Funkcjonalności](#funkcjonalności)
- [Role użytkowników](#role-użytkowników)
- [Baza danych](#baza-danych)
- [API Endpoints](#api-endpoints)
- [Dane logowania (testowe)](#dane-logowania-testowe)

## Technologie

- **Backend:** PHP 8.x (obiektowy, bez frameworka)
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS, Fetch API)
- **Baza danych:** PostgreSQL 15
- **Konteneryzacja:** Docker, Docker Compose
- **Serwer WWW:** Nginx
- **Ikony:** Iconify

## Wymagania

- Docker Desktop (Windows/Mac) lub Docker Engine + Docker Compose (Linux)
- Git
- Przeglądarka internetowa (Chrome, Firefox, Edge)

## Instalacja i uruchomienie

### 1. Klonowanie repozytorium

```bash
git clone https://github.com/[twoj-username]/harcapp.git
cd harcapp
```

### 2. Konfiguracja środowiska

Skopiuj plik przykładowych zmiennych środowiskowych:

```bash
cp .env.example .env
```

### 3. Uruchomienie aplikacji

```bash
docker-compose up -d
```

Aplikacja będzie dostępna pod adresami:
- **Aplikacja:** http://localhost:8080
- **pgAdmin:** http://localhost:5050

### 4. Inicjalizacja bazy danych

Przy pierwszym uruchomieniu wykonaj migracje SQL w pgAdmin:
1. Zaloguj się do pgAdmin (admin@harcapp.pl / admin123)
2. Połącz się z serwerem PostgreSQL (host: postgres, user: harcapp, password: harcapp_secret)
3. Wykonaj skrypty z katalogu `database/migrations/` w kolejności numeracji

### 5. Zatrzymanie aplikacji

```bash
docker-compose down
```

## Struktura projektu

```
harcapp/
├── database/
│   └── migrations/           # Skrypty SQL (migracje, widoki, triggery)
├── nginx/
│   └── nginx.conf            # Konfiguracja serwera Nginx
├── php/
│   └── Dockerfile            # Obraz PHP z rozszerzeniami
├── public/
│   ├── css/
│   │   └── style.css         # Główny arkusz stylów
│   ├── js/
│   │   ├── validation.js     # Walidacja formularzy (Fetch API)
│   │   ├── game-create.js    # Logika tworzenia gier
│   │   └── game-date-validation.js
│   ├── uploads/              # Pliki przesłane przez użytkowników
│   └── index.php             # Punkt wejścia aplikacji
├── src/
│   ├── controllers/          # Kontrolery MVC
│   │   ├── AdminController.php
│   │   ├── GameController.php
│   │   ├── HomeController.php
│   │   ├── SecurityController.php
│   │   └── SettingsController.php
│   ├── repository/           # Warstwa dostępu do danych (Singleton)
│   │   ├── UserRepository.php
│   │   └── GameRepository.php
│   ├── views/                # Widoki (szablony PHP)
│   │   ├── admin/
│   │   ├── game/
│   │   ├── home/
│   │   ├── login.php
│   │   └── register.php
│   └── Routing.php           # Router aplikacji
├── docker-compose.yml        # Definicja usług Docker
├── .env.example              # Przykładowe zmienne środowiskowe
└── README.md                 # Dokumentacja
```

## Architektura

Aplikacja wykorzystuje architekturę **MVC (Model-View-Controller)**:

```
┌─────────────────────────────────────────────────────────────┐
│                        PRZEGLĄDARKA                         │
│                    (HTML, CSS, JavaScript)                  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                          NGINX                              │
│                    (Reverse Proxy, Port 8080)               │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        PHP-FPM                              │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Routing   │→ │ Controllers │→ │      Views          │  │
│  │  (Router)   │  │   (Logika)  │  │ (Szablony PHP/HTML) │  │
│  └─────────────┘  └──────┬──────┘  └─────────────────────┘  │
│                          │                                   │
│                          ▼                                   │
│                 ┌─────────────────┐                          │
│                 │  Repositories   │                          │
│                 │ (Dostęp do BD)  │                          │
│                 └────────┬────────┘                          │
└──────────────────────────┼──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      POSTGRESQL                             │
│           (Baza danych, Port 5432 wewnętrzny)               │
└─────────────────────────────────────────────────────────────┘
```

### Wzorce projektowe

- **MVC** - separacja logiki, widoków i danych
- **Singleton** - pojedyncza instancja repozytoriów (UserRepository, GameRepository)
- **Front Controller** - jeden punkt wejścia (index.php)

## Funkcjonalności

### Użytkownik niezalogowany
- Rejestracja z walidacją (Fetch API sprawdza dostępność emaila)
- Logowanie

### Uczestnik (uczestnik)
- Przeglądanie dostępnych gier
- Podgląd szczegółów gry
- Zmiana hasła

### Twórca (tworca)
- Wszystkie uprawnienia uczestnika
- Tworzenie nowych gier
- Edycja własnych gier
- Zarządzanie punktami kontrolnymi i zespołami
- Usuwanie gier

### Administrator (admin)
- Wszystkie uprawnienia twórcy
- Zarządzanie użytkownikami
- Zmiana ról użytkowników
- Zatwierdzanie/odrzucanie rejestracji
- Usuwanie użytkowników

## Role użytkowników

| Rola | Kod w BD | Uprawnienia |
|------|----------|-------------|
| Administrator | `admin` | Pełne zarządzanie systemem |
| Twórca | `tworca` | Tworzenie i zarządzanie grami |
| Punktowy | `punktowy` | Przyznawanie punktów (w rozwoju) |
| Uczestnik | `uczestnik` | Podstawowy dostęp do gier |

## Baza danych

### Tabele główne

- `users` - użytkownicy systemu
- `games` - gry harcerskie
- `game_teams` - zespoły/zastępy w grach
- `game_points` - punkty kontrolne
- `game_participants` - przypisanie uczestników do gier/zespołów
- `game_scorers` - przypisanie punktowych do gier
- `game_scores` - wyniki punktacji
- `game_audit_log` - log zmian (trigger)

### Relacje

- **1:N** - users → games (twórca gry)
- **1:N** - games → game_teams, game_points
- **N:M** - users ↔ games (przez game_participants, game_scorers)

### Widoki SQL

- `view_games_summary` - podsumowanie gier z liczbą zespołów, punktów, uczestników
- `view_team_rankings` - ranking zespołów w grach

### Funkcje SQL

- `get_team_total_points(game_id, team_id)` - suma punktów zespołu
- `get_game_winner(game_id)` - zwycięzca gry

### Triggery

- `game_changes_trigger` - loguje INSERT/UPDATE/DELETE na tabeli games

## API Endpoints

### Publiczne

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/` | Strona główna / Dashboard |
| GET | `/security/login` | Formularz logowania |
| POST | `/security/login` | Logowanie |
| GET | `/security/register` | Formularz rejestracji |
| POST | `/security/register` | Rejestracja |
| POST | `/security/checkEmail` | Sprawdzenie dostępności emaila (Fetch API) |
| GET | `/security/logout` | Wylogowanie |

### Wymagające autoryzacji

| Metoda | Endpoint | Opis | Rola |
|--------|----------|------|------|
| GET | `/game` | Lista gier | wszystkie |
| GET | `/game/view?id=X` | Szczegóły gry | wszystkie |
| GET | `/game/create` | Formularz tworzenia gry | tworca, admin |
| POST | `/game/createPost` | Zapisanie nowej gry | tworca, admin |
| GET | `/game/edit?id=X` | Formularz edycji gry | tworca, admin |
| POST | `/game/editPost` | Zapisanie zmian gry | tworca, admin |
| POST | `/game/delete` | Usunięcie gry | tworca, admin |
| GET | `/admin` | Panel administracyjny | admin |
| POST | `/admin/changeRole` | Zmiana roli użytkownika | admin |
| POST | `/admin/deleteUser` | Usunięcie użytkownika | admin |
| GET | `/settings` | Ustawienia użytkownika | wszystkie |
| POST | `/settings/changePassword` | Zmiana hasła | wszystkie |

## Dane logowania (testowe)

| Email | Hasło | Rola |
|-------|-------|------|
| admin@harcapp.pl | admin123 | Administrator |

## Zmienne środowiskowe

Plik `.env` (lub `.env.example`):

```env
# PostgreSQL
POSTGRES_USER=harcapp
POSTGRES_PASSWORD=harcapp_secret
POSTGRES_DB=harcapp_db

# pgAdmin
PGADMIN_DEFAULT_EMAIL=admin@harcapp.pl
PGADMIN_DEFAULT_PASSWORD=admin123
```

## Autor

Projekt wykonany w ramach przedmiotu WdPAI.

## Licencja

Projekt edukacyjny - do użytku akademickiego.
