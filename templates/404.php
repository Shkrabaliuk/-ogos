<?php
/**
 * 404 Not Found Template
 */

// Ensure blogSettings are loaded for the header title if needed
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

$pageTitle = "404 — Сторінку не знайдено";
require __DIR__ . '/header.php';
?>

<div class="error-container">
    <h1 class="error-code">404</h1>
    <p class="error-message">Тут нічого немає.</p>
    <p class="error-joke">Можливо, ця сторінка втекла з екрану, щоб не платити податки.</p>
</div>

<style>
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
        padding: 2rem;
    }

    .error-code {
        font-size: 8rem;
        font-weight: 900;
        margin: 0;
        line-height: 1;
        background: linear-gradient(45deg, var(--text-color), #666);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        opacity: 0.8;
    }

    .error-message {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 1rem 0 0.5rem 0;
    }

    .error-joke {
        font-size: 1rem;
        color: #777;
        margin-bottom: 2rem;
        font-style: italic;
    }
</style>

<?php require __DIR__ . '/footer.php'; ?>