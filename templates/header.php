<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $blogTitle) ?></title>

    <?php if (!empty($blogSettings['blog_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($blogSettings['blog_description']) ?>">
    <?php endif; ?>

    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($blogTitle) ?> RSS Feed"
        href="/rss.php">

    <!-- CSS: Base utilities + Theme -->
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/theme.css">

    <?php if (!empty($blogSettings['google_analytics_id'])): ?>
        <!-- Google Analytics -->
        <script async
            src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>');
        </script>
    <?php endif; ?>
</head>

<body>

    <?php
    use App\Services\Auth;
    $isAdmin = Auth::check();

    // Завантажуємо налаштування блогу
    // Завантажуємо налаштування блогу
    if (!isset($blogSettings)) {
        // Fallback for legacy calls or pure PHP includes
        // If $pdo is not global (e.g. inside a function/router closure), we get it from Singleton
        if (!isset($pdo)) {
            $pdo = \App\Config\Database::connect();
        }
        $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
        $blogSettings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $blogSettings[$row['key']] = $row['value'];
        }
    }
    $blogTitle = $blogSettings['blog_title'] ?? '/\ogos';
    ?>

    <header>
        <h1>
            <a href="/"><?= htmlspecialchars($blogTitle) ?></a>
        </h1>

        <?php if (!empty($blogSettings['blog_tagline'])): ?>
            <p><?= htmlspecialchars($blogSettings['blog_tagline']) ?></p>
        <?php endif; ?>

        <nav>
            <a href="/">Головна</a>
            <?php if ($isAdmin): ?>
                <a href="#" onclick="toggleNewPostForm(); return false;">+ Новий пост</a>
                <a href="/admin/settings">Налаштування</a>
                <a href="/api/logout.php">Вийти</a>
            <?php else: ?>
                <a href="#" id="loginToggle">Увійти</a>
            <?php endif; ?>
            <a href="/rss.php">RSS</a>

            <form action="/search.php" method="get">
                <input type="search" name="q" placeholder="Пошук..." required />
            </form>
        </nav>
    </header>
    <main>