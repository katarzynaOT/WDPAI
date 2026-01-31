<?php
// Expected variables: $topPanel, $leftMenu, $centerView, $role
?>
<div style="font-family: Arial, sans-serif;">
    <header style="background:#2c3e50;color:#fff;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="<?= htmlspecialchars($topPanel['logo'] ?? '/public/images/logo.png') ?>" alt="logo" style="height:36px;object-fit:contain;">
            <strong>Moja Aplikacja</strong>
        </div>
        <div>
            <?php if (!empty($topPanel['showSearch'])): ?>
                <input type="search" placeholder="Szukaj...">
            <?php endif; ?>
        </div>
    </header>

    <div style="display:flex;height:calc(100vh - 64px);">
        <nav style="width:220px;background:#ecf0f1;padding:16px;box-sizing:border-box;">
            <ul style="list-style:none;padding:0;margin:0;">
                <?php foreach ($leftMenu as $item): ?>
                    <li style="margin-bottom:8px;"><a href="/<?= htmlspecialchars($item['path']) ?>" style="text-decoration:none;color:#2c3e50;"><?= htmlspecialchars($item['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <main style="flex:1;padding:20px;overflow:auto;">
            <?php
            // Include center view (e.g. 'student/dashboard')
            $centerPath = __DIR__ . '/../' . $centerView . '.php';
            if (file_exists($centerPath)) {
                require $centerPath;
            } else {
                echo '<h2>Dashboard</h2><p>Brak zawartości.</p>';
            }
            ?>
        </main>
    </div>
</div>
