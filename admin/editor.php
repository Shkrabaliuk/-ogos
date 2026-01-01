<?php
// admin/editor.php - Редактор постів

require_once '../config/db.php';

// Перевірка: чи редагуємо існуючий пост
$post_id = $_GET['id'] ?? null;
$post = null;

if ($post_id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
}

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';
    $type = $_POST['type'] ?? 'text';
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    if ($post_id) {
        // Оновлення
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET title = ?, slug = ?, content = ?, type = ?, is_published = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $slug, $content, $type, $is_published, $post_id]);
    } else {
        // Створення
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, slug, content, type, is_published) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $slug, $content, $type, $is_published]);
        $post_id = $pdo->lastInsertId();
    }
    
    // Редирект на сторінку поста
    header("Location: /{$slug}");
    exit;
}

$pageTitle = $post ? "Редагувати: {$post['title']}" : "Новий пост";
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — /\ogos Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="/assets/fonts/tildasans.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/libs/fontawesome/css/all.min.css">
    <style>
        .form-control {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: var(--headingsColor);
        }
        
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            font-family: inherit;
            font-size: 16px;
            background: var(--inputBackgroundColor);
            border: 1px solid var(--thinRuleColor);
            border-radius: 4px;
        }
        
        textarea {
            min-height: 400px;
            font-family: monospace;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: var(--hoverColor);
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .button:hover {
            background: #d04848;
        }
        
        .button-secondary {
            background: #999;
        }
        
        .button-secondary:hover {
            background: #777;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1 style="margin: 0;"><?= $post ? 'Редагувати пост' : 'Новий пост' ?></h1>
        <a href="/" class="button button-secondary">
            <i class="fas fa-arrow-left"></i> Назад до блогу
        </a>
    </div>
    
    <form method="POST">
        <div class="form-control">
            <label class="form-label" for="title">Заголовок</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                required
                autofocus
            >
        </div>
        
        <div class="form-control">
            <label class="form-label" for="slug">Slug (URL)</label>
            <input 
                type="text" 
                id="slug" 
                name="slug" 
                value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                required
                pattern="[a-z0-9\-]+"
                placeholder="example-post-url"
            >
            <small style="color: #999;">Тільки латиниця, цифри та дефіси</small>
        </div>
        
        <div class="form-control">
            <label class="form-label" for="content">Контент (Neasden розмітка)</label>
            <textarea 
                id="content" 
                name="content" 
                required
            ><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
            <small style="color: #999; margin-top: 8px; display: block;">
                <strong>Синтаксис Neasden:</strong><br>
                # Заголовок, ## Підзаголовок<br>
                - список • **жирний** • //курсив//<br>
                Блок коду: пусті рядки до/після, відступ 4 пробіли<br>
                &gt; Цитата (на початку рядка)
            </small>
        </div>
        
        <div class="form-control">
            <label class="form-label" for="type">Тип поста</label>
            <select id="type" name="type">
                <option value="text" <?= ($post['type'] ?? 'text') === 'text' ? 'selected' : '' ?>>Text</option>
                <option value="image" <?= ($post['type'] ?? '') === 'image' ? 'selected' : '' ?>>Image</option>
                <option value="link" <?= ($post['type'] ?? '') === 'link' ? 'selected' : '' ?>>Link</option>
                <option value="quote" <?= ($post['type'] ?? '') === 'quote' ? 'selected' : '' ?>>Quote</option>
            </select>
        </div>
        
        <div class="form-control">
            <label class="checkbox-wrapper">
                <input 
                    type="checkbox" 
                    name="is_published" 
                    <?= ($post['is_published'] ?? 1) ? 'checked' : '' ?>
                >
                <span>Опублікувати</span>
            </label>
        </div>
        
        <div clasi class="fas fa-save"></i>
                <?= $post ? 'Зберегти зміни' : 'Створити пост' ?>
            </button>
            
            <?php if ($post): ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="button button-secondary" style="margin-left: 10px;">
                    <i class="fas fa-eye"></i>f ($post): ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="button button-secondary" style="margin-left: 10px;">
                    Переглянути
                </a>
            <?php endif; ?>
        </div>
    </form>
    
    <script>
        // Автоматичне заповнення slug з заголовка
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        titleInput.addEventListener('input', () => {
            if (!slugInput.value || slugInput.dataset.auto !== 'false') {
                const slug = titleInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s\-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                slugInput.value = slug;
            }
        });
        
        slugInput.addEventListener('input', () => {
            slugInput.dataset.auto = 'false';
        });
    </script>
</div>

</body>
</html>
