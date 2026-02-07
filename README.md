# Platofrma do korepetycji

Aplikacja internetowa łącząca uczniów i korepetytorów. Umożliwia rezerwację lekcji, zarządzanie profilami, wystawianie opinii oraz przeglądanie dostępnych tutorów. System wspiera dwie role użytkowników: student (uczeń) oraz tutor (korepetytor).

Aplikacja posiada solidną podstawę i jest otwarta na wiele nowych, potencjalnych rozszerzeń. Projekt został zaprojektowany w sposób umożliwiający dalszy rozwój, z wyraźnym podziałem na odpowiedzialności.

# Instrukcja uruchomienia

1. Skonfiguruj plik `config.env` z danymi dostępowymi do bazy (host, user, password, dbname).
2. Upewnij się, że Docker oraz docker-compose są zainstalowane.
3. Uruchom polecenie:
   ```bash
   docker-compose up --build
   ```
4. Aplikacja będzie dostępna pod adresem `localhost`.
5. Domyślne dane logowania można ustawić w pliku `init.sql` lub przez rejestrację.

# 1. Architektura + struktura (controller, repo, service, models, views)

Projekt oparty o wzorzec MVC (Model-View-Controller):
- **Controllers**: obsługa logiki aplikacji, routing, autoryzacja, dashboard, profile, booking, review, subject, error. Każdy kontroler odpowiada za wybraną funkcjonalność.
- **Models**: reprezentacja danych użytkowników, bookingów, lekcji, opinii. Modele mapują strukturę bazy na obiekty PHP.
- **Repositories**: warstwa dostępu do bazy (CRUD, relacje, referencje). Każdy model ma dedykowany repozytorium, np. UserRepository, BookingRepository.
- **Services**: logika biznesowa (np. logowanie, rejestracja, profile, wyszukiwanie tutorów, obsługa sesji). Oddzielają logikę od kontrolerów.
- **Views**: pliki PHP/HTML generujące interfejs użytkownika. Widoki podzielone na role (student/tutor), dashboardy, profile, formularze.
- **Public**: statyczne pliki JS, CSS, obrazy. Skrypty JS obsługują dynamiczne akcje (fetch API, AJAX), style CSS zapewniają responsywność.

Przykładowa struktura katalogów:
- `src/controllers/` – kontrolery
- `src/models/` – modele
- `src/repositories/` – repozytoria
- `src/services/` – serwisy
- `src/views/` – widoki PHP
- `public/views/` – widoki HTML
- `public/js/` – skrypty JS
- `public/css/` – style CSS

# 2. Technologie i narzędzia (wymagania systaemowe)

- **PHP 8+** – backend, programowanie obiektowe
- **PostgreSQL** – baza danych relacyjna
- **Docker, docker-compose** – uruchamianie, izolacja środowiska, automatyzacja deploymentu
- **JavaScript (ES6+)** – frontend, dynamiczna obsługa interfejsu, fetch API/AJAX
- **HTML5, CSS3** – interfejs użytkownika, responsywność, media queries
- **Nginx** – reverse proxy, obsługa ruchu HTTP
- **PDO** – bezpieczna komunikacja z bazą danych

# 3. Baza danych

Aplikacja wykorzystuje klasę Database opartą o wzorzec **Singleton**, zapewniającą jedno współdzielone połączenie **PDO z bazą PostgreSQL**. Konfiguracja połączenia jest dostosowana do środowiska kontenerowego Docker.

Baza danych PostgreSQL:
- Schemat zdefiniowany w pliku `docker/db/init.sql`.
- Tabele: user, tutor, student, booking, lesson, review, subject, homework.
- Relacje: booking powiązany z user, lesson, review powiązany z booking i user.
- Akcje na referencjach: ON DELETE/UPDATE CASCADE/RESTRICT (zapewnia spójność danych przy usuwaniu/aktualizacji).
- Widoki, funkcje, transakcje: (do uzupełnienia, jeśli są w init.sql; można dodać np. widok listy aktywnych lekcji, funkcję liczby opinii dla tutora, transakcje przy rezerwacji lekcji).
- Eksport bazy: plik `init.sql` (możliwość odtworzenia struktury i danych).

![ERD](./diagram_ERD.png)

# 4. Użytkownicy (Role, sesja, uprawnienia, autoryzacja)

- Role użytkowników: student, tutor (różne uprawnienia, widoki, akcje)
- Logowanie, rejestracja, sesja użytkownika (PHP session, $_SESSION)
- Uprawnienia: dostęp do panelu, edycja profilu, rezerwacja lekcji, wystawianie opinii, przeglądanie tutorów
- Autoryzacja: kontrolery AuthorizationController, ProfileController, sprawdzanie uprawnień w kodzie
- Wylogowywanie: obsługiwane przez backend (usuwanie sesji)
- Ochrona widoków: sprawdzanie roli i uprawnień przed wyświetleniem strony

1. **Student** - uczeń   
   - Może przeglądać tutorów, rezerwować lekcje, edytować swój profil, wystawiać opinie.
2. **Tutor** - korepetytor
   - Może zarządzać swoim profilem, akceptować rezerwacje, przeglądać opinie, edytować lekcje.

# 5. Funkcjonalność

- Rejestracja i logowanie użytkowników (formularze, walidacja, obsługa sesji)
- Przeglądanie profili tutorów (wyszukiwanie, filtrowanie, sortowanie)
- Rezerwacja lekcji (booking, wybór terminu, potwierdzenie, transakcje)
- Edycja profilu (student/tutor, zmiana danych, zdjęcie profilowe)
- Wystawianie opinii (review, ocena, komentarz)
- Zarządzanie lekcjami (dodawanie, edycja, lista, status lekcji)
- Panel studenta i tutora (dashboard, statystyki, lista rezerwacji, powiadomienia)
- Responsywny interfejs (CSS, media queries, dostosowanie do urządzeń mobilnych)
- AJAX/fetch API do dynamicznej obsługi (np. rezerwacje, edycja profilu, pobieranie listy tutorów bez przeładowania strony)
- Obsługa błędów i komunikatów (ErrorController, wyświetlanie informacji użytkownikowi)

# 6. Bezpieczenstwo

- Zmienne środowiskowe w pliku `.env` (bezpieczne przechowywanie danych, brak danych w kodzie)
- Walidacja danych wejściowych (PHP, JS; sprawdzanie poprawności, filtrowanie, sanitizacja)
- Uprawnienia i role użytkowników (sprawdzanie dostępu, ograniczenie akcji do wybranych ról)
- Sesja użytkownika (PHP session, ochrona przed nieautoryzowanym dostępem)
- Ograniczenie dostępu do wybranych widoków (sprawdzanie uprawnień w kontrolerach)
- Brak replikacji kodu (DRY, wspólne klasy, serwisy)
- Ochrona przed atakami typu SQL Injection (PDO, prepared statements)
- Ochrona przed XSS (filtrowanie danych wyjściowych)
- Szyfrowanie haseł (hashowanie, np. password_hash)

# API/Routing

Routing oparty o kontrolery PHP. Przykładowe endpointy:
- `/login` - logowanie (POST)
- `/register` - rejestracja (POST)
- `/dashboard` - panel użytkownika (GET)
- `/profile/edit` - edycja profilu (POST/GET)
- `/booking/create` - rezerwacja lekcji (POST)
- `/review/create` - wystawianie opinii (POST)
- `/tutors` - lista tutorów (GET)
- `/lessons` - lista lekcji (GET)

Routing w aplikacji jest realizowany przez klasę Router, która dynamicznie rejestruje trasy i dopasowuje ścieżki URL do odpowiednich kontrolerów oraz metod. Mechanizm obsługuje parametry w adresach (np. :id) i pozwala na zarządzanie trasami bez użycia tablic routingu.

# Możliwe rozszerzenia

1. Dodanie logów
2. Ulepszenie designu (jasny-ciemny motyw, motyw dla słabowidzących, lepsza responsywność)
3. Ulepszenie przechwycania błędów




