<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Логування помилки 404
$errorLogFile = 'logs/404_errors.log';
$errorMessage = date('Y-m-d H:i:s') . ' - 404 Error: ' . $_SERVER['REQUEST_URI'] . ' - IP: ' . $_SERVER['REMOTE_ADDR'] . "\n";
file_put_contents($errorLogFile, $errorMessage, FILE_APPEND);

$pageTitle = "Сторінку не знайдено";
require 'includes/templates/header.php';
?>

<main class="container">
    <div class="error-404">
        <h1>404</h1>
        <h2>Сторінку не знайдено</h2>
        <p>На жаль, сторінка, яку ви шукаєте, не існує або була видалена.</p>
        
        <div class="search-form">
            <form action="index.php" method="GET">
                <input type="text" name="search" placeholder="Пошук на сайті..." required>
                <button type="submit">Шукати</button>
            </form>
        </div>
        
        <div class="useful-links">
            <h3>Корисні посилання</h3>
            <ul>
                <li><a href="index.php">Головна сторінка</a></li>
                <li><a href="post.php">Популярні пости</a></li>
                <li><a href="admin/login.php">Вхід в адмінку</a></li>
            </ul>
        </div>
    </div>
</main>

<?php require 'includes/templates/footer.php'; ?>