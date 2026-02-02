<!DOCTYPE html>
<html>
<head>
    <title>Tututor</title>
</head>
<body>

<header>
    <a href="/">Home</a>

    <?php if (isset($_SESSION['user'])): ?>
        <a href="/logout">Logout</a>
    <?php else: ?>
        <a href="/login">Login</a>
    <?php endif; ?>
</header>

<main>
    <?= $content ?>
</main>

</body>
</html>
