<?php
session_start();
require '../includes/db.php';
require '../includes/functions.php';

if (!is_admin()) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'] ?? null;
$post = ['title' => '', 'content' => '', 'tags' => ''];

if ($id) {
    $post = get_post($id);
    if (!$post) {
        header("Location: admin.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, tags = ? WHERE id = ?");
        $stmt->execute([$title, $content, $tags, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, tags) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $tags]);
    }

    header("Location: admin.php");
    exit;
}

$blog_name = get_setting('blog_name', 'Блог');
$theme = get_setting('theme_color', 'blue');
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редагування' : 'Новий пост' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-blog"></i>
                <?= htmlspecialchars($blog_name) ?>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                Дашборд
            </a>
            <a href="post-editor.php" class="nav-item active">
                <i class="fas fa-pen"></i>
                Новий пост
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                Налаштування
            </a>
            <a href="../index.php" class="nav-item">
                <i class="fas fa-home"></i>
                На сайт
            </a>
        </nav>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <h1>
                <i class="fas fa-<?= $id ? 'edit' : 'plus' ?>"></i>
                <?= $id ? 'Редагування поста' : 'Новий пост' ?>
            </h1>
        </div>

        <form method="POST">
            <div class="card">
                <div class="form-group">
                    <input type="text" 
                           name="title" 
                           class="form-control" 
                           value="<?= htmlspecialchars($post['title']) ?>" 
                           placeholder="Заголовок поста..."
                           required
                           autofocus
                           style="font-size: 28px; font-weight: 600; border: none; padding: 0;">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tags"></i>
                        Теги
                    </label>
                    <input type="text" 
                           name="tags" 
                           class="form-control" 
                           value="<?= htmlspecialchars($post['tags']) ?>" 
                           placeholder="тег1, тег2, тег3">
                    <div class="form-hint">Розділяйте теги комами</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left"></i>
                        Контент
                        <span style="float: right; font-weight: normal; color: var(--text-muted); font-size: 12px;">
                            Markdown: **жирний**, *курсив*, # заголовок, [текст](url)
                        </span>
                    </label>
                    <textarea name="content" 
                              class="form-control" 
                              placeholder="Текст вашого поста..." 
                              style="min-height: 400px; font-family: 'Monaco', 'Courier New', monospace; font-size: 14px;"
                              required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;">
                        <i class="fas fa-save"></i>
                        Зберегти
                    </button>
                    <a href="admin.php" class="btn" style="font-size: 16px; padding: 12px 24px;">
                        <i class="fas fa-times"></i>
                        Скасувати
                    </a>
                </div>
            </div>
        </form>
    </main>
</div>

<script src="../assets/js/theme.js"></script>
</body>
</html>
