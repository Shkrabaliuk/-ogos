<?php
session_start();
require '../includes/db.php';
require '../includes/functions.php';

if (!is_admin()) {
    header("Location: admin.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_name = trim($_POST['blog_name'] ?? '');
    $blog_subtitle = trim($_POST['blog_subtitle'] ?? '');
    $posts_per_page = intval($_POST['posts_per_page'] ?? 10);
    $footer_text = trim($_POST['footer_text'] ?? '');
    $footer_engine = trim($_POST['footer_engine'] ?? '');

    if (empty($blog_name)) {
        $error = 'Назва блогу обов\'язкова';
    } else {
        set_setting('blog_name', $blog_name);
        set_setting('blog_subtitle', $blog_subtitle);
        set_setting('posts_per_page', $posts_per_page);
        set_setting('footer_text', $footer_text);
        set_setting('footer_engine', $footer_engine);

        // Завантаження аватарки
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['avatar']['type'];
            
            if (in_array($file_type, $allowed)) {
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . time() . '.' . $ext;
                $upload_path = '../assets/images/' . $filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    // Видалити стару аватарку
                    $old_avatar = get_setting('avatar');
                    if ($old_avatar && file_exists('..' . $old_avatar)) {
                        unlink('..' . $old_avatar);
                    }
                    
                    set_setting('avatar', '/assets/images/' . $filename);
                } else {
                    $error = 'Помилка завантаження файлу';
                }
            } else {
                $error = 'Дозволені тільки зображення (JPG, PNG, GIF, WEBP)';
            }
        }

        if (empty($error)) {
            $success = 'Налаштування збережено!';
        }
    }
}

$blog_name = get_setting('blog_name', 'Мій Блог');
$blog_subtitle = get_setting('blog_subtitle', 'Підзаголовок');
$posts_per_page = get_setting('posts_per_page', 10);
$footer_text = get_setting('footer_text', '© Автор блогу');
$footer_engine = get_setting('footer_engine', 'Рушій — Мій');
$avatar = get_setting('avatar', '');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Налаштування</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h1>Налаштування блогу</h1>
        <a href="admin.php" class="btn">← Назад</a>
    </div>

    <div class="admin-nav">
        <a href="admin.php">📝 Пости</a>
        <a href="settings.php" class="active">⚙️ Налаштування</a>
    </div>

    <?php if ($success): ?>
        <div class="success-message"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Аватарка</label>
            <div class="avatar-upload">
                <?php if ($avatar): ?>
                    <img src="<?= htmlspecialchars($avatar) ?>" class="avatar-preview">
                <?php else: ?>
                    <div class="avatar-preview"></div>
                <?php endif; ?>
                <div>
                    <input type="file" name="avatar" accept="image/*" id="avatarInput" style="display: none;">
                    <button type="button" class="btn" onclick="document.getElementById('avatarInput').click()">
                        <?= $avatar ? 'Змінити' : 'Завантажити' ?>
                    </button>
                    <div class="form-hint">JPG, PNG, GIF або WEBP. Макс 2MB</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Назва блогу *</label>
            <input type="text" name="blog_name" class="form-control" value="<?= htmlspecialchars($blog_name) ?>" required>
        </div>

        <div class="form-group">
            <label>Підзаголовок</label>
            <input type="text" name="blog_subtitle" class="form-control" value="<?= htmlspecialchars($blog_subtitle) ?>">
            <div class="form-hint">Короткий опис вашого блогу</div>
        </div>

        <div class="form-group">
            <label>Постів на сторінку</label>
            <select name="posts_per_page" class="form-control">
                <option value="5" <?= $posts_per_page == 5 ? 'selected' : '' ?>>5</option>
                <option value="10" <?= $posts_per_page == 10 ? 'selected' : '' ?>>10</option>
                <option value="15" <?= $posts_per_page == 15 ? 'selected' : '' ?>>15</option>
                <option value="20" <?= $posts_per_page == 20 ? 'selected' : '' ?>>20</option>
            </select>
        </div>

        <div class="form-group">
            <label>Текст футера</label>
            <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($footer_text) ?>">
            <div class="form-hint">Наприклад: © Ваше ім'я</div>
        </div>

        <div class="form-group">
            <label>Назва рушія</label>
            <input type="text" name="footer_engine" class="form-control" value="<?= htmlspecialchars($footer_engine) ?>">
            <div class="form-hint">Наприклад: Рушій — Мій CMS</div>
        </div>

        <button type="submit" class="btn btn-primary">💾 Зберегти налаштування</button>
    </form>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

</body>
</html>
