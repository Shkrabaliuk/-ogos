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
    
    <link rel="stylesheet" href="/assets/css/style.css">
    
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
        <?php include $childView; ?>
    </main>

    <footer>
        <div class="flex-between">
            <div>
                Powered by /\ogos — <?= date('Y') ?>
            </div>
            <div class="flex-center">
                <a href="/rss.php" title="RSS Feed" class="inline-flex-link">
                    <i class="fas fa-rss"></i>
                    RSS
                </a>
                <?php if ($isAdmin): ?>
                    <a href="/admin/settings.php" title="Налаштування" class="auth-icon">
                        <i class="fas fa-cog"></i>
                    </a>
                    <a href="/logout.php" title="Вийти" class="auth-icon logout-link">
                        <i class="fas fa-unlock"></i>
                    </a>
                <?php else: ?>
                    <a href="/login.php" title="Адміністрування" class="auth-icon">
                        <i class="fas fa-lock"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/assets/libs/fotorama/fotorama.js"></script>

<!-- Moment.js для роботи з датами -->
<script src="/assets/libs/momentjs/moment-with-locales.min.js"></script>
<script>
    // Встановлюємо українську локаль
    moment.locale('uk');
</script>

<!-- Highlight.js для підсвічування коду -->
<script src="/assets/libs/highlight/highlight.js"></script>
<script>
    // Автоматичне підсвічування всіх блоків коду
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('pre code').forEach(function(block) {
            hljs.highlightElement(block);
        });
    });
</script>

<!-- Search toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('searchToggle');
        const search = document.getElementById('headerSearch');
        const input = document.getElementById('searchInput');
        
        toggle.addEventListener('click', function() {
            search.classList.toggle('active');
            if (search.classList.contains('active')) {
                setTimeout(() => input.focus(), 300);
            }
        });
        
        // Закрити при кліку поза пошуком
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !search.contains(e.target)) {
                search.classList.remove('active');
            }
        });
        
        // Закрити при натисканні Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                search.classList.remove('active');
            }
        });
    });
</script>

</body>
</html>