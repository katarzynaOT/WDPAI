<h1>Witaj w Dashboardzie Ucznia</h1>

<p>Cześć, <?= htmlspecialchars($_SESSION['user_first_name'] ?? 'Uczeń') ?>!</p>

<p>Tu możesz:</p>
<ul>
    <li><a href="/search">Szukać korepetytorów</a></li>
    <li><a href="/profile">Zarządzać profilem</a></li>
    <li><a href="/logout">Wylogować się</a></li>
</ul>