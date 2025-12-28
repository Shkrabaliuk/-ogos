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
    $theme_color = $_POST['theme_color'] ?? 'blue';

    if (empty($blog_name)) {
        $error = 'Назва блогу обов\'язкова';
    } else {
        set_setting('blog_name', $blog_name);
        set_setting('blog_subtitle', $blog_subtitle);
        set_setting('posts_per_page', $posts_per_page);
        set_setting('footer_text', $footer_text);
        set_setting('footer_engine', $footer_engine);
        set_setting('theme_color', $theme_color);

        // Завантаження аватарки
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['avatar']['type'];
            
            if (in_array($file_type, $allowed)) {
                $upload_dir = '../assets/images';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . time() . '.' . $ext;
                $upload_path = $upload_dir . '/' . $filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    $old_avatar = get_setting('avatar');
                    if ($old_avatar && file_exists('..' . $old_avatar)) {
                        unlink('..' . $old_avatar);
                    }
                    set_setting('avatar', '/assets/images/' . $filename);
                } else {
                    $error = 'Помилка завантаження файлу';
                }
            } else {
                $error = 'Дозволені тільки зображення';
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
$theme_color = get_setting('theme_color', 'blue');

$themes = [
    'blue' => '#3B82F6',
    'purple' => '#8B5CF6',
    'green' => '#10B981',
    'orange' => '#F59E0B',
    'red' => '#EF4444',
    'pink' => '#EC4899'
];
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?= $theme_color ?>">
<head>
    <meta charset="UTF-8">
    <title>Налаштування</title>
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
            <a href="post-editor.php" class="nav-item">
                <i class="fas fa-pen"></i>
                Новий пост
            </a>
            <a href="settings.php" class="nav-item active">
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
            <h1>
                <i class="fas fa-cog"></i>
                Налаштування
            </h1>
            <p>Персоналізація вашого блогу</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="card">
                <h3 style="margin-bottom: 24px;">
                    <i class="fas fa-palette"></i>
                    Зовнішній вигляд
                </h3>

                <div class="form-group">
                    <label class="form-label">Аватарка</label>
                    <div class="avatar-upload">
                        <?php if ($avatar): ?>
                            <img src="<?= htmlspecialchars($avatar) ?>" class="avatar-preview" id="avatarPreview">
                        <?php else: ?>
                            <div class="avatar-preview" id="avatarPreview"></div>
                        <?php endif; ?>
                        <div>
                            <input type="file" name="avatar" accept="image/*" id="avatarInput" style="display: none;">
                            <button type="button" class="btn" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-upload"></i>
                                <?= $avatar ? 'Змінити' : 'Завантажити' ?>
                            </button>
                            <div class="form-hint">JPG, PNG, GIF або WEBP. Макс 2MB</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Акцентний колір теми</label>
                    <div class="color-picker">
                        <?php foreach ($themes as $name => $color): ?>
                            <div class="color-option <?= $theme_color === $name ? 'active' : '' ?>" 
                                 style="background: <?= $color ?>;"
                                 data-theme="<?= $name ?>"
                                 onclick="selectColor('<?= $name ?>')">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="theme_color" id="themeColorInput" value="<?= $theme_color ?>">
                    <div class="form-hint">Виберіть колір, який буде використовуватись по всьому сайту</div>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 24px;">
                    <i class="fas fa-info-circle"></i>
                    Основна інформація
                </h3>

                <div class="form-group">
                    <label class="form-label">Назва блогу *</label>
                    <input type="text" name="blog_name" class="form-control" value="<?= htmlspecialchars($blog_name) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Підзаголовок</label>
                    <input type="text" name="blog_subtitle" class="form-control" value="<?= htmlspecialchars($blog_subtitle) ?>">
                    <div class="form-hint">Короткий опис вашого блогу</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Постів на сторінку</label>
                    <select name="posts_per_page" class="form-control">
                        <option value="5" <?= $posts_per_page == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $posts_per_page == 10 ? 'selected' : '' ?>>10</option>
                        <option value="15" <?= $posts_per_page == 15 ? 'selected' : '' ?>>15</option>
                        <option value="20" <?= $posts_per_page == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 24px;">
                    <i class="fas fa-align-left"></i>
                    Футер
                </h3>

                <div class="form-group">
                    <label class="form-label">Текст футера</label>
                    <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($footer_text) ?>">
                    <div class="form-hint">Наприклад: © Ваше ім'я</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Назва рушія</label>
                    <input type="text" name="footer_engine" class="form-control" value="<?= htmlspecialchars($footer_engine) ?>">
                    <div class="form-hint">Наприклад: Рушій — Мій CMS</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;">
                <i class="fas fa-save"></i>
                Зберегти налаштування
            </button>
        </form>
    </main>
</div>

<script src="../assets/js/theme.js"></script>
<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-preview';
                img.id = 'avatarPreview';
                preview.replaceWith(img);
            }
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

function selectColor(theme) {
    document.getElementById('themeColorInput').value = theme;
    document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
    document.querySelector(`.color-option[data-theme="${theme}"]`).classList.add('active');
    changeTheme(theme);
}
</script>

</body>
</html>
