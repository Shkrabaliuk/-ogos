<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions.php';

$blog_name = get_setting('blog_name', 'Мій Блог');
$blog_subtitle = get_setting('blog_subtitle', 'Сучасна платформа для блогінгу');
$avatar = get_setting('avatar', '');
$theme = get_setting('theme_color', 'blue');
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= htmlspecialchars($blog_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="site-wrapper">
    <header class="site-header">
        <div class="header-content">
            <a href="/index.php" class="logo">
                <?php if ($avatar): ?>
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="logo-avatar">
                <?php else: ?>
                    <div class="logo-avatar"></div>
                <?php endif; ?>
                <div class="logo-text">
                    <h1><?= htmlspecialchars($blog_name) ?></h1>
                    <p><?= htmlspecialchars($blog_subtitle) ?></p>
                </div>
            </a>

            <nav class="header-nav">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           class="search-input" 
                           placeholder="Пошук постів..."
                           onkeyup="if(event.key==='Enter' && this.value) window.location.href='/index.php?search='+encodeURIComponent(this.value)">
                </div>

                <?php if (is_admin()): ?>
                    <a href="/admin/post-editor.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Новий пост
                    </a>
                    <a href="/admin/admin.php" class="btn btn-ghost">
                        <i class="fas fa-tachometer-alt"></i>
                        Дашборд
                    </a>
                    <a href="/admin/admin.php?logout=1" class="btn btn-ghost">
                        <i class="fas fa-sign-out-alt"></i>
                        Вийти
                    </a>
                <?php else: ?>
                    <a href="/admin/admin.php" class="btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Увійти
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
