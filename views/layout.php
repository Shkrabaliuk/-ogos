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