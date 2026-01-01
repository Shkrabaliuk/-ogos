<!DOCTYPE html>
<html lang="uk" class="dark-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '/\ogos') ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    
    <!-- Tilda Sans Font -->
    <link rel="stylesheet" href="/assets/fonts/tildasans.css">
    
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Fotorama gallery -->
    <link rel="stylesheet" href="/assets/libs/fotorama/fotorama.css">
    
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="/assets/libs/fontawesome/css/all.min.css">
</head>
<body>

<div class="container">
    <header>
        <div class="logo">
            <a href="/">/\ogos <span>blog</span></a>
        </div>
        
        <form method="GET" action="/search.php" class="header-search">
            <input 
                type="search" 
                name="q" 
                placeholder="Пошук..."
                class="search-input-mini"
            >
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </header>

    <main>
        <?php include $childView; ?>
    </main>

    <footer>
        Powered by /\ogos — <?= date('Y') ?>
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

</body>
</html>
