<?php
// admin/index.php - Список всіх постів

require_once '../config/db.php';

// Отримання всіх постів (включно з непублікованими)
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

$pageTitle = "Адмін-панель";
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — /\ogos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="/assets/fonts/tildasans.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: var(--hoverColor);
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .button:hover {
            background: #d04848;
        }
        
        .posts-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .posts-table th,
        .posts-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--thinRuleColor);
        }
        
        .posts-table th {
            font-weight: bold;
            color: var(--headingsColor);
        }
        
        .posts-table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-published {
            background: #d4edda;
            color: #155724;
        }
        
        .status-draft {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .action-link {
            color: var(--linkColor);
            text-decoration: none;
            font-size: 14px;
        }
        
        .action-link:hover {
            color: var(--hoverColor);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1 style="margin: 0;">Адмін-панель — Пости</h1>
        <div style="display: flex; gap: 10px;">
            <a href="/" class="button">
                <i class="fas fa-home"></i> Блог
            </a>
            <a href="/admin/settings.php" class="button" style="background: #999;">
                <i class="fas fa-cog"></i> Налаштування
            </a>
        </div>
    </div>
    
    <?php if (empty($posts)): ?>
        <p style="color: #999;">Постів ще немає. <a href="/">Перейти на головну</a> щоб створити перший.</p>
    <?php else: ?>
        <table class="posts-table">
            <thead>
                <tr>
                    <th>Заголовок</th>
                    <th>Slug</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($post['title']) ?></strong>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($post['slug']) ?></code>
                        </td>
                        <td>
                            <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($post['is_published']): ?>
                                <span class="status-badge status-published">Опубліковано</span>
                            <?php else: ?>
                                <span class="status-badge status-draft">Чернетка</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="/<?= htmlspecialchars($post['slug']) ?>" class="action-link" target="_blank">
                                <i class="fas fa-eye"></i> Переглянути
                            </a>
                            <a href="/<?= htmlspecialchars($post['slug']) ?>#edit" class="action-link">
                                <i class="fas fa-edit"></i> Редагувати
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
