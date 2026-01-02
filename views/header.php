<!DOCTYPE html>
<html lang="uk" class="dark-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '/\ogos') ?></title>
    
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
</head>
<body>

<?php
// Підключаємо авторизацію
require_once __DIR__ . '/../includes/auth.php';
$isAdmin = isLoggedIn();
?>

<div class="container">
    <header>
        <div class="logo">
            <a href="/">/\ogos <span>blog</span></a>
        </div>
        
        <div class="header-search-container">
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
