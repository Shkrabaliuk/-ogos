<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../functions.php';

$blog_name = get_setting('blog_name', 'Мій Блог');
$blog_subtitle = get_setting('blog_subtitle', '');
$blog_description = get_setting('blog_description', '');
$avatar = get_setting('avatar', '');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= htmlspecialchars(generate_page_title($pageTitle ?? '', $blog_name)) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($blog_description): ?>
<meta name="description" content="<?= htmlspecialchars($blog_description) ?>" />
<meta property="og:description" content="<?= htmlspecialchars($blog_description) ?>" />
<?php endif; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
<link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($blog_name) ?> RSS" href="/rss.php" />
</head>
<body>

<div class="common">

<div class="flag">
  <div class="header-content">
    <div class="header-description">
      <div class="title">
        <div class="title-inner">
          
          <div class="logo-marginal">
            <?php if ($avatar): ?>
              <a href="/index.php"><img src="<?= htmlspecialchars($avatar) ?>" alt="" class="logo-avatar" /></a>
            <?php else: ?>
              <div class="logo-avatar"></div>
            <?php endif; ?>
          </div>

          <div class="logo">
            <?php if ($avatar): ?>
              <a href="/index.php"><img src="<?= htmlspecialchars($avatar) ?>" alt="" class="logo-avatar" /></a>
            <?php else: ?>
              <div class="logo-avatar"></div>
            <?php endif; ?>
          </div>

          <h1><a href="/index.php"><?= htmlspecialchars($blog_name) ?></a></h1>
          <?php if ($blog_subtitle): ?>
            <p><?= htmlspecialchars($blog_subtitle) ?></p>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <div class="spotlight">
      <span class="admin-links-floating">
        <span class="admin-menu admin-links">
          
          <span class="admin-icon" title="Пошук по тегах">
            <a href="/tags.php" class="nu"><i class="fas fa-tags"></i></a>
          </span>

          <?php if (is_admin()): ?>
            <span class="admin-icon" title="Керування постами">
              <a href="/admin/posts.php" class="nu"><i class="fas fa-file-alt"></i></a>
            </span>
            <span class="admin-icon" title="Модерація коментарів">
              <a href="/admin/comments.php" class="nu"><i class="fas fa-comments"></i></a>
            </span>
            <span class="admin-icon" title="Налаштування">
              <a href="/admin/settings.php" class="nu"><i class="fas fa-cog"></i></a>
            </span>
          <?php endif; ?>

        </span>

        <form class="e2-search-box-nano" action="/index.php" method="get">
          <label>
            <input class="js-search-query" type="search" name="search" value="" placeholder="Пошук" />
            <span class="e2-search-icon">
              <i class="fas fa-search"></i>
            </span>
          </label>
        </form>

      </span>
    </div>
  </div>
</div>
