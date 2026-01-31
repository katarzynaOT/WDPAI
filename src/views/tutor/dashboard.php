<h1>Witaj w Dashboardzie Korepetytora</h1>

<p>Cześć, <?= htmlspecialchars($_SESSION['user_first_name'] ?? 'Korepetytor') ?>!</p>

<p>Tu możesz:</p>
<ul>
    <li><a href="/profile">Zarządzać profilem</a></li>
    <li><a href="/students">Zobacz uczniów</a></li>
    <li><a href="/logout">Wylogować się</a></li>
</ul>