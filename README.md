# Platofrma do korepetycji

Aplikacja internetowa łącząca uczniów i korepetytorów.

# Instrukcja uruchomienia

1. Skonfiguruj plik `config.env` z danymi dostępowymi do bazy.
2. Uruchom polecenie:
   ```bash
   docker-compose up --build
   ```
3. Aplikacja będzie dostępna pod adresem `localhost` (domyślnie port 80).

# 1. Architektura + struktura (controller, repo, service, models, views)

Projekt oparty o wzorzec MVC:
- **Controllers**: obsługa logiki aplikacji, routing, autoryzacja, dashboard, profile, booking, review, subject, error.
- **Models**: reprezentacja danych użytkowników, bookingów.
- **Repositories**: warstwa dostępu do bazy (CRUD, relacje, referencje).
- **Services**: logika biznesowa (np. logowanie, rejestracja, profile, wyszukiwanie tutorów).
- **Views**: pliki PHP/HTML generujące interfejs użytkownika.
- **Public**: statyczne pliki JS, CSS, obrazy.

# 2. Technologie i narzędzia (wymagania systaemowe)

- **PHP 8+** (backend, obiektowo)
- **PostgreSQL** (baza danych)
- **Docker, docker-compose** (uruchamianie, izolacja środowiska)
- **JavaScript** (frontend, AJAX/fetch API)
- **HTML5, CSS3** (interfejs, responsywność)
- **Nginx** (reverse proxy)

# 3. Baza danych

Aplikacja wykorzystuje klasę Database opartą o wzorzec **Singleton**, zapewniającą jedno współdzielone połączenie **PDO z bazą PostgreSQL**. Konfiguracja połączenia jest dostosowana do środowiska kontenerowego Docker.

Baza danych PostgreSQL:
- Schemat zdefiniowany w pliku `docker/db/init.sql`.
- Złożone relacje: booking, user, review, subject, homework.
- Akcje na referencjach: ON DELETE/UPDATE CASCADE/RESTRICT.
- Widoki, funkcje, transakcje: (do uzupełnienia, jeśli są w init.sql).
- Eksport bazy: plik `init.sql`.

# 4. Użytkownicy (Role, sesja, uprawnienia, autoryzacja)

- Role użytkowników: student, tutor (różne uprawnienia, widoki, akcje)
- Logowanie, rejestracja, sesja użytkownika (PHP session)
- Uprawnienia: dostęp do panelu, edycja profilu, rezerwacja lekcji, wystawianie opinii
- Autoryzacja: kontrolery AuthorizationController, ProfileController
- Wylogowywanie: obsługiwane przez backend

1. **Student** - uczeń   
2. **Tutor** - korepetytor

# 5. Funkcjonalność

- Rejestracja i logowanie użytkowników
- Przeglądanie profili tutorów
- Rezerwacja lekcji (booking)
- Edycja profilu (student/tutor)
- Wystawianie opinii (review)
- Zarządzanie lekcjami (dodawanie, edycja, lista)
- Panel studenta i tutora (dashboard)
- Responsywny interfejs (CSS, media queries)
- AJAX/fetch API do dynamicznej obsługi (np. rezerwacje, edycja profilu)

# 6. Bezpieczenstwo

- Zmienne środowiskowe w pliku `.env` (bezpieczne przechowywanie danych)
- Walidacja danych wejściowych (PHP, JS)
- Uprawnienia i role użytkowników
- Sesja użytkownika (PHP session)
- Ograniczenie dostępu do wybranych widoków
- Brak replikacji kodu (DRY)

# API/Routing

Routing oparty o kontrolery PHP. Przykładowe endpointy:
- `/login` - logowanie
- `/register` - rejestracja
- `/dashboard` - panel użytkownika
- `/profile/edit` - edycja profilu
- `/booking/create` - rezerwacja lekcji
- `/review/create` - wystawianie opinii

