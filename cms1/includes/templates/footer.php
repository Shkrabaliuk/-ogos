    <footer class="site-footer">
        <div>
            <?= htmlspecialchars(get_setting('footer_text', '© Мій Блог')) ?>, <?= date('Y') ?>
        </div>
        <div class="footer-links">
            <a href="#"><?= htmlspecialchars(get_setting('footer_engine', 'Рушій — CMS')) ?></a>
            <?php if (is_admin()): ?>
                <a href="/admin/settings.php"><i class="fas fa-cog"></i> Налаштування</a>
            <?php endif; ?>
        </div>
    </footer>
</div>

<script src="/assets/js/theme.js"></script>
</body>
</html>
