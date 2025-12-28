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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід в адмінку</title>
    <link rel="stylesheet" href="../assets/css/install.css">
</head>
<body>
<div class="install-container">
    <div class="install-icon">
        <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
            <circle cx="40" cy="40" r="40" fill="#F4B942"/>
            <text x="40" y="55" font-size="40" text-anchor="middle" fill="white">🔐</text>
        </svg>
    </div>
    <h1><?= $userExists ? 'Вхід' : 'Створення адміна' ?></h1>
    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <input type="password" name="password" placeholder="Введіть пароль" required autofocus>
        </div>
        <button type="submit" class="install-button">
            <?= $userExists ? 'Увійти' : 'Створити' ?>
        </button>
    </form>
    <p style="text-align: center; margin-top: 20px;">
        <a href="../index.php" style="color: #666;">← На головну</a>
    </p>
</div>
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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін-панель</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h1><?= htmlspecialchars($blog_name) ?></h1>
        <div style="display: flex; gap: 12px;">
            <a href="../index.php" class="btn">← На сайт</a>
            <a href="?logout=1" class="btn">Вийти</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="admin.php" class="active">📝 Пости</a>
        <a href="settings.php">⚙️ Налаштування</a>
        <a href="post-editor.php" class="btn btn-primary">+ Новий пост</a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <p>У вас ще немає постів</p>
            <br>
            <a href="post-editor.php" class="btn btn-primary">Створити перший пост</a>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-list-item">
                <div>
                    <a href="../post.php?id=<?= $post['id'] ?>" style="font-weight: 600; color: var(--text); text-decoration: none;">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                    <div style="font-size: 13px; color: var(--subtext); margin-top: 4px;">
                        <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="post-editor.php?id=<?= $post['id'] ?>" class="btn">Ред.</a>
                    <a href="?delete=<?= $post['id'] ?>" onclick="return confirm('Видалити?')" style="color: #d00; text-decoration: none;">Вид.</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
