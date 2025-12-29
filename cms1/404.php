<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
$pageTitle = "404 — Сторінку не знайдено";
require 'includes/templates/header.php';
?>

<main class="site-main">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="empty-state" style="padding: 80px 20px;">
            <i class="fas fa-search" style="font-size: 80px;"></i>
            <h1 style="font-size: 48px; margin-bottom: 16px;">404</h1>
            <h3>Сторінку не знайдено</h3>
            <p>Схоже, що сторінка, яку ви шукаєте, не існує</p>
            <a href="/index.php" class="btn btn-primary" style="margin-top: 24px;">
                <i class="fas fa-home"></i>
                На головну
            </a>
        </div>
    </div>
</main>

<?php require 'includes/templates/footer.php'; ?>
