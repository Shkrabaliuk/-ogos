<?php
session_start();

if (!file_exists('../config.php')) {
    header("Location: ../install/install.php");
    exit;
}

require '../includes/db.php';
require '../includes/functions.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

$userExists = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];

    if (!$userExists) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (password) VALUES (?)");
        $stmt->execute([$hash]);
        $_SESSION['is_admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['is_admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Невірний пароль";
        }
    }
}

if (!is_admin()) {
    $theme = get_setting('theme_color', 'blue');
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--bg);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .login-icon {
            text-align: center;
            font-size: 64px;
            color: var(--accent);
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="card login-card">
        <div class="login-icon">
            <i class="fas fa-lock"></i>
        </div>
        <h1 style="text-align: center; margin-bottom: 32px;">
            <?= $userExists ? 'Вхід в адмінку' : 'Створення адміна' ?>
        </h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i>
                <?= $userExists ? 'Увійти' : 'Створити' ?>
            </button>
        </form>
        <p style="text-align: center; margin-top: 24px;">
            <a href="../index.php" style="color: var(--text-muted);">
                <i class="fas fa-arrow-left"></i>
                На головну
            </a>
        </p>
    </div>
</div>
<script src="../assets/js/theme.js"></script>
</body>
</html>
<?php
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin.php");
    exit;
}

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
$blog_name = get_setting('blog_name', 'Адмін-панель');
$theme = get_setting('theme_color', 'blue');
$total_posts = count($posts);
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <title>Дашборд</title>
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
            <a href="admin.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                Дашборд
            </a>
            <a href="post-editor.php" class="nav-item">
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
            <a href="?logout=1" class="nav-item" style="margin-top: 24px; color: var(--danger);">
                <i class="fas fa-sign-out-alt"></i>
                Вийти
            </a>
        </nav>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <h1>Дашборд</h1>
            <p>Керування вашим блогом</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 32px;">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="color: var(--text-muted); font-size: 14px;">Всього постів</p>
                        <h2 style="font-size: 32px; font-weight: 600; margin-top: 8px;"><?= $total_posts ?></h2>
                    </div>
                    <div style="width: 64px; height: 64px; background: var(--accent-light); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-alt" style="font-size: 28px; color: var(--accent);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i>
                    Всі пости
                </h2>
                <a href="post-editor.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Новий пост
                </a>
            </div>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Немає постів</h3>
                    <p>Створіть свій перший пост</p>
                    <a href="post-editor.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Створити пост
                    </a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <th style="text-align: left; padding: 12px; font-weight: 600;">Заголовок</th>
                                <th style="text-align: left; padding: 12px; font-weight: 600;">Дата</th>
                                <th style="text-align: left; padding: 12px; font-weight: 600;">Теги</th>
                                <th style="text-align: right; padding: 12px; font-weight: 600;">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 12px;">
                                        <a href="../post.php?id=<?= $post['id'] ?>" style="color: var(--text); text-decoration: none; font-weight: 500;">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </td>
                                    <td style="padding: 12px; color: var(--text-muted); font-size: 14px;">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if (!empty($post['tags'])): ?>
                                            <?php foreach (array_slice(parse_tags($post['tags']), 0, 2) as $tag): ?>
                                                <span class="tag" style="margin-right: 4px;">
                                                    <i class="fas fa-tag"></i>
                                                    <?= htmlspecialchars($tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: right;">
                                        <a href="post-editor.php?id=<?= $post['id'] ?>" class="btn" style="margin-right: 8px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?= $post['id'] ?>" onclick="return confirm('Видалити?')" class="btn" style="color: var(--danger);">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="../assets/js/theme.js"></script>
</body>
</html>
