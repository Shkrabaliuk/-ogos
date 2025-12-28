<?php
$footer_text = get_setting('footer_text', '© Автор блогу');
$footer_engine = get_setting('footer_engine', 'Рушій — Мій');
$current_year = date('Y');
?>

<footer>
    <div class="container">
        <p><?= htmlspecialchars($footer_text) ?>, <?= $current_year ?></p>
        <div class="footer-links">
            <a href="#"><?= htmlspecialchars($footer_engine) ?></a>
            <?php if (is_admin()): ?>
                <a href="/admin/settings.php">⚙️ Налаштування</a>
                <a href="/admin/admin.php">📝 Адмінка</a>
            <?php endif; ?>
        </div>
    </div>
</footer>

</body>
</html>
