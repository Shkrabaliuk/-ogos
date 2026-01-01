<div class="footer">
  © <span id="e2-blog-author"><?= htmlspecialchars(get_setting('author_name') ?: get_setting('footer_text', 'Автор блогу')) ?></span>, <?= date('Y') ?>
  <a class="e2-rss-button" href="/rss.php" title="RSS"><i class="fas fa-rss"></i></a>

  <?php if (!is_admin()): ?>
    <a class="e2-visual-login nu" href="/admin/login.php" title="Вхід">
      <span class="e2-admin-link">
        <i class="fas fa-lock"></i>
      </span>
    </a>
  <?php else: ?>
    <a class="e2-visual-login nu" href="/admin/settings.php?logout" title="Вихід">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  <?php endif; ?>

  <div class="engine">
    <span title="Рушій блогу">Рушій — <a href="#" class="nu"><u><?= htmlspecialchars(get_setting('footer_engine', 'Егея')) ?></u></a></span>
  </div>
</div>

</div>

</body>
</html>
