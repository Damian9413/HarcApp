# HarcApp – Docker w Docker Desktop

## Co masz w projekcie

- **docker-compose.yml** – opisuje 4 serwisy: PostgreSQL, pgAdmin, PHP, Nginx  
- **nginx/nginx.conf** – konfiguracja Nginx (katalog `public` jako root)  
- **php/Dockerfile** – obraz PHP 8.2 z rozszerzeniem PDO PostgreSQL  
- **public/index.php** – prosty test, czy wszystko działa  

---

## Krok 1: Uruchom Docker Desktop

1. Otwórz **Docker Desktop** (z menu lub Spotlight).  
2. Poczekaj, aż w pasku zobaczysz **Docker is running** (zielona kropka).  
3. Nie musisz otwierać żadnego okna – wystarczy, że Docker działa w tle.  

---

## Krok 2: Otwórz terminal w katalogu projektu

1. Otwórz **Terminal** (np. w Cursor: Terminal → New Terminal).  
2. Przejdź do katalogu projektu:
   ```bash
   cd /Users/damian/Desktop/Studia/WdPAI/wdpai_projekt
   ```
3. Sprawdź, że jest tam `docker-compose.yml`:
   ```bash
   ls -la
   ```

---

## Krok 3: Uruchom kontenery

W tym samym katalogu wykonaj:

```bash
docker-compose up -d
```

- **up** – uruchamia serwisy  
- **-d** – w tle („detached”), terminal się zwolni  

Pierwszy raz może potrwać 1–2 minuty (ściąganie obrazów).  
Na końcu zobaczysz coś w stylu:

```
✔ Container harcapp_postgres  Started
✔ Container harcapp_php      Started
✔ Container harcapp_nginx    Started
✔ Container harcapp_pgadmin  Started
```

---

## Krok 4: Sprawdź, że wszystko działa

### A) Aplikacja (PHP + Nginx)

W przeglądarce wejdź na:

**http://localhost:8080**

Powinna się załadować strona z tekstem „HarcApp”, informacją o wersji PHP i „PDO PostgreSQL: tak”.

### B) Docker Desktop – lista kontenerów

1. Otwórz **Docker Desktop**.  
2. Po lewej wybierz **Containers**.  
3. Powinieneś zobaczyć 4 kontenery: **harcapp_nginx**, **harcapp_php**, **harcapp_postgres**, **harcapp_pgadmin**.  
4. Przy każdym status powinien być **running** (zielona kropka).  

---

## Krok 5: pgAdmin – pierwsze wejście i połączenie z bazą

### 5.1 Wejście do pgAdmin

1. W przeglądarce wejdź na: **http://localhost:5050**  
2. **Email:** `admin@harcapp.local`  
3. **Hasło:** `admin123`  
4. Zaloguj się (może pojawić się prośba o ustawienie hasła master – możesz pominąć lub ustawić swoje).  

### 5.2 Dodanie serwera PostgreSQL

1. Prawy przycisk na **Servers** → **Register** → **Server**.  
2. Zakładka **General**:  
   - **Name:** np. `HarcApp Local` (dowolna nazwa).  
3. Zakładka **Connection**:  
   - **Host:** `postgres` (nazwa serwisu z docker-compose, nie localhost).  
   - **Port:** `5432`  
   - **Maintenance database:** `harcapp_db`  
   - **Username:** `harcapp`  
   - **Password:** `harcapp_secret`  
   - Zaznacz **Save password** (opcjonalnie).  
4. **Save**.  

Jeśli połączenie się uda, pod **Servers** pojawi się **HarcApp Local**, a pod nim baza **harcapp_db**.  
Na razie będzie pusta – tabele dodamy w kolejnym etapie.  

---

## Przydatne komendy

| Co chcesz zrobić | Komenda |
|------------------|--------|
| Zatrzymać wszystko | `docker-compose down` |
| Zatrzymać i usunąć dane bazy | `docker-compose down -v` |
| Uruchomić ponownie | `docker-compose up -d` |
| Zobaczyć logi (np. Nginx) | `docker-compose logs -f nginx` |
| Wejść do konsoli PHP | `docker-compose exec php sh` |
| Wejść do konsoli bazy | `docker-compose exec postgres psql -U harcapp -d harcapp_db` |

---

## Porty (żeby nie kolidowały z innymi programami)

- **8080** – aplikacja (Nginx)  
- **5050** – pgAdmin  
- **5432** – PostgreSQL (dostęp z komputera, np. dla DataGrip)  

Jeśli któryś port jest zajęty, w `docker-compose.yml` zmień np. `"8080:80"` na `"9080:80"` i wtedy aplikacja będzie na **http://localhost:9080**.  

---

## Gdy coś nie działa

1. **Strona localhost:8080 nie działa**  
   - Sprawdź w Docker Desktop, czy **harcapp_nginx** i **harcapp_php** są **running**.  
   - Sprawdź logi: `docker-compose logs nginx` i `docker-compose logs php`.  

2. **pgAdmin nie łączy się z bazą**  
   - W Connection upewnij się, że **Host** to `postgres`, a nie `localhost`.  
   - Sprawdź, czy kontener **harcapp_postgres** ma status **running**.  

3. **„Port already in use”**  
   - Inna aplikacja używa tego portu. Zmień lewą liczbę w `ports` w `docker-compose.yml` (np. 8080 na 9080).  

Jak skończysz te kroki, napisz czy **http://localhost:8080** pokazuje testową stronę i czy w pgAdmin widzisz bazę **harcapp_db**. Wtedy przejdziemy do tworzenia tabel.
