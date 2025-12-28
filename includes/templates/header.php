<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions.php';

$blog_name = get_setting('blog_name', 'Назва блогу');
$blog_subtitle = get_setting('blog_subtitle', 'Підзаголовок');
$avatar = get_setting('avatar', '');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= htmlspecialchars($blog_name) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header>
    <div class="container">
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

            <div class="header-search">
                <input type="text" 
                       class="search-input" 
                       id="searchInput" 
                       placeholder="Пошук..."
                       onkeyup="if(event.key==='Enter') window.location.href='/?search='+encodeURIComponent(this.value)">
                <button class="search-icon" onclick="toggleSearch()">🔍</button>
            </div>
        </div>
    </div>
</header>

<script>
function toggleSearch() {
    const input = document.getElementById('searchInput');
    input.classList.toggle('active');
    if (input.classList.contains('active')) {
        input.focus();
    }
}

document.addEventListener('click', function(e) {
    const search = document.querySelector('.header-search');
    if (!search.contains(e.target)) {
        document.getElementById('searchInput').classList.remove('active');
    }
});
</script>
