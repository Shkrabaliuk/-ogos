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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редагування' : 'Новий пост' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h1><?= $id ? 'Редагування поста' : 'Новий пост' ?></h1>
        <a href="admin.php" class="btn">← Скасувати</a>
    </div>

    <form method="POST">
        <div class="form-group">
            <input type="text" 
                   name="title" 
                   class="form-control" 
                   value="<?= htmlspecialchars($post['title']) ?>" 
                   placeholder="Заголовок поста..."
                   required
                   autofocus
                   style="font-size: 24px; font-weight: 600;">
        </div>

        <div class="form-group">
            <label>Теги</label>
            <input type="text" 
                   name="tags" 
                   class="form-control" 
                   value="<?= htmlspecialchars($post['tags']) ?>" 
                   placeholder="тег1, тег2, тег3">
            <div class="form-hint">Розділяйте теги комами</div>
        </div>

        <div class="form-group">
            <label style="display: flex; justify-content: space-between; align-items: center;">
                <span>Контент</span>
                <span style="font-size: 12px; font-weight: normal; color: var(--subtext);">
                    Підтримка Markdown: **жирний**, *курсив*, # заголовок, [текст](url)
                </span>
            </label>
            <textarea name="content" 
                      class="form-control" 
                      placeholder="Текст вашого поста..." 
                      required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">💾 Зберегти</button>
            <a href="admin.php" class="btn">Скасувати</a>
        </div>
    </form>
</div>

</body>
</html>
