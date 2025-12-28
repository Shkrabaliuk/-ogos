<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CMS4Blog' ?></title>
    <link rel="stylesheet" href="/public/assets/css/main.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><?= $siteName ?? 'CMS4Blog' ?></h1>
            <nav>
                <a href="/">Головна</a>
                <a href="/about">Про систему</a>
                <a href="/contact">Контакти</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?= $content ?? '' ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= $siteName ?? 'CMS4Blog' ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
