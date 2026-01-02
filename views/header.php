<!DOCTYPE html>
<html lang="uk" class="dark-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '/\ogos') ?></title>
    
    <?php if (!empty($blogSettings['blog_description'])): ?>
    <meta name="description" content="<?= htmlspecialchars($blogSettings['blog_description']) ?>">
    <?php endif; ?>
    
    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($pageTitle ?? '/\ogos') ?> RSS Feed" href="/rss.php">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    
    <!-- Tilda Sans Font -->
    <link rel="stylesheet" href="/assets/fonts/tildasans.css">
    
    <!-- Main CSS (minified) -->
    <?php if (defined('ENV') && ENV === 'development'): ?>
        <link rel="stylesheet" href="/assets/css/style.css">
    <?php else: ?>
        <link rel="stylesheet" href="/assets/minify.php?f=style.css&t=css&v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <?php endif; ?>
    
    <!-- FontAwesome icons (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <?php if (!empty($blogSettings['google_analytics_id'])): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>');
    </script>
    <?php endif; ?>
</head>
<body>

<?php
// Підключаємо авторизацію
require_once __DIR__ . '/../includes/auth.php';
$isAdmin = isLoggedIn();

// Завантажуємо налаштування блогу
if (!isset($blogSettings)) {
    global $pdo;
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
    $blogSettings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $blogSettings[$row['key']] = $row['value'];
    }
}
$blogTitle = $blogSettings['blog_title'] ?? '/\ogos';
?>

<div class="container">
    <header>
        <div class="logo">
            <a href="/"><?= htmlspecialchars($blogTitle) ?> <span>blog</span></a>
        </div>
        
        <div class="header-search-container">
            <?php if ($isAdmin): ?>
            <button type="button" class="search-toggle" onclick="toggleNewPostForm()" title="Новий пост">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>
            <button type="button" class="search-toggle" id="searchToggle">
                <i class="fas fa-search"></i>
            </button>
            <form method="GET" action="/search.php" class="header-search" id="headerSearch">
                <input 
                    type="search" 
                    name="q" 
                    placeholder="Пошук..."
                    class="search-input-mini"
                    id="searchInput"
                >
            </form>
        </div>
    </header>

    <main>
