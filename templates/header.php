<?php
use App\Services\Auth;
$isAdmin = Auth::check();

if (!isset($blogSettings)) {
    if (!isset($pdo)) {
        $pdo = \App\Config\Database::connect();
    }
    $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
    $blogSettings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $blogSettings[$row['key']] = $row['value'];
    }
}
$blogTitle = $blogSettings['site_title'] ?? '/\\ogos';
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $blogTitle) ?></title>

    <?php if (!empty($blogSettings['site_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($blogSettings['site_description']) ?>">
    <?php endif; ?>

    <link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($blogTitle) ?> RSS Feed" href="/rss.php">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/theme.css">

    <style>
        /* Header Layout */
        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 2rem 0;
            border-bottom: 3px solid var(--text-color);
            margin-bottom: 2rem;
        }

        /* Left Side: Brand & Nav */
        .brand-container {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            max-width: 60%;
        }

        .brand-container h1 {
            margin: 0;
            line-height: 1;
            font-size: 2rem;
        }

        .brand-container p.tagline {
            margin: 0;
            font-size: 1rem;
            color: #666;
        }

        .main-nav {
            margin-top: 1rem;
            display: flex;
            gap: 1.5rem;
        }

        .main-nav a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .main-nav a:hover {
            color: var(--accent);
        }

        /* Right Side: Icons */
        .header-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px; /* Fixed small gap */
            padding-top: 0.5rem;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            margin: 0;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit; /* Default inherits */
            transition: color 0.2s;
        }

        .icon-btn svg {
            width: 22px;
            height: 22px;
            stroke-width: 2;
        }

        /* Admin Icons specific style */
        .icon-btn.admin-icon {
            color: #2e7d32; /* Green color */
        }
        
        .icon-btn.admin-icon:hover {
            color: #1b5e20; /* Darker green on hover */
        }

        /* Search Icon specific style */
        .icon-btn.search-icon {
            color: #000;
        }
        
        .icon-btn.search-icon:hover {
            opacity: 0.7;
        }

        /* Admin Controls Group */
        .admin-group {
            display: flex;
            gap: 8px;
            /* No hiding logic anymore */
        }

        /* Search Interaction */
        #searchForm {
            display: flex;
            align-items: center;
        }

        #searchInput {
            width: 0;
            opacity: 0;
            border: none;
            background: transparent;
            border-bottom: 1px solid transparent;
            padding: 0;
            transition: width 0.3s ease, margin-right 0.3s ease, opacity 0.3s ease;
            font-size: 1rem;
            outline: none;
        }

        #searchInput.active {
            width: 150px;
            opacity: 1;
            border-bottom: 1px solid var(--text-color);
            margin-right: 8px; /* Gap between input and search button */
            padding: 4px 0;
        }
    </style>

    <?php if (!empty($blogSettings['google_analytics_id'])): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?= htmlspecialchars($blogSettings['google_analytics_id']) ?>');
        </script>
    <?php endif; ?>
</head>

<body>

    <header>
        <div class="brand-container">
            <h1><a href="/"><?= htmlspecialchars($blogTitle) ?></a></h1>
            
            <?php if (!empty($blogSettings['blog_tagline'])): ?>
                <p class="tagline"><?= htmlspecialchars($blogSettings['blog_tagline']) ?></p>
            <?php endif; ?>

            <nav class="main-nav">
                <a href="/about">Про мене</a>
                <a href="/archive">Архів</a>
            </nav>
        </div>

        <div class="header-controls" id="headerControls">
            <?php if ($isAdmin): ?>
                <div class="admin-group">
                    <a href="/admin/new-post" class="icon-btn admin-icon" title="Новий пост">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </a>
                    <a href="/admin/settings" class="icon-btn admin-icon" title="Налаштування">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </a>
                </div>
            <?php endif; ?>

            <form action="/search.php" method="get" id="searchForm">
                <input type="search" name="q" id="searchInput" placeholder="Пошук..." required autocomplete="off">
                <button type="button" class="icon-btn search-icon" id="searchToggle" title="Пошук">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>
        </div>
    </header>
    <main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchToggle = document.getElementById('searchToggle');
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');

            if (searchToggle && searchInput) {
                searchToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    if (searchInput.classList.contains('active')) {
                        if (searchInput.value.trim().length > 0) {
                            searchForm.submit();
                        } else {
                            searchInput.classList.remove('active');
                        }
                    } else {
                        searchInput.classList.add('active');
                        searchInput.focus();
                    }
                });

                searchInput.addEventListener('blur', () => {
                    setTimeout(() => {
                        if (document.activeElement !== searchInput && searchInput.value.trim() === '') {
                            searchInput.classList.remove('active');
                        }
                    }, 200);
                });
            }
        });
    </script>