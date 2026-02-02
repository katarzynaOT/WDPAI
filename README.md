# Platofrma dla korepetytorów

Aplikacja internetowa łącząac uczniów i korepetytorów.

** Instalacja i uruchomienie **

# 1. Architektura + struktura (controller, repo, service, models, views)

# 2. Technologie i narzędzia (wymagania systaemowe)

# 3. Baza danych (ERD, init.sql, akcje na refenrecjach?)

# 4. Użytkownicy (Role, sesja, uprawnienia, autoryzacja)

# 5. Funkcjonalność

# 6. Bezpieczenstwo

** API/Routing** przy postamanie

# Reszta

Aplikacja uruchamiana jest w środowisku kontenerowym Docker z użyciem docker-compose.

Aplikacja wykorzystuje klasę Database opartą o wzorzec Singleton, zapewniającą jedno współdzielone połączenie PDO z bazą PostgreSQL. Konfiguracja połączenia jest dostosowana do środowiska kontenerowego Docker.

Konfiguracja aplikacji została oparta o plik .env, który przechowuje zmienne środowiskowe takie jak dane dostępowe do bazy danych. Zmienne te są ładowane do aplikacji za pomocą własnego loadera i dostępne poprzez tablicę $_ENV. Takie podejście zwiększa bezpieczeństwo oraz umożliwia łatwą zmianę konfiguracji bez modyfikacji kodu źródłowego.