<?php
// install.php

// 1. ОБРОБКА ЗАПИТУ ВІД JS (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Вказуємо, що відповідаємо у форматі JSON
    header('Content-Type: application/json');
    
    $server = $_POST['server'] ?? 'localhost';
    $user = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $database = $_POST['database'] ?? '';

    try {
        // Пробуємо підключитися до MySQL
        $dsn = "mysql:host=$server;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Створюємо базу даних, якщо її немає
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
        $pdo->exec("USE `$database`");

        // Створюємо таблицю для постів
        $sql = "CREATE TABLE IF NOT EXISTS `posts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `content` text NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);

        // Записуємо дані у файл config.php
        $configContent = "<?php
define('DB_HOST', '$server');
define('DB_NAME', '$database');
define('DB_USER', '$user');
define('DB_PASS', '$password');
";
        if (file_put_contents('config.php', $configContent) === false) {
             throw new Exception("Не вдалося створити файл config.php. Перевірте права запису.");
        }

        // Повертаємо успіх
        echo json_encode(['success' => true]);
        exit;

    } catch (Exception $e) {
        // Повертаємо помилку
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 2. ВІДОБРАЖЕННЯ (Твій HTML)
$alreadyInstalled = file_exists('config.php');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення - CMS4Blog</title>
    <link rel="stylesheet" href="assets/css/install.css">
</head>
<body>
    <div class="container">
        <?php if ($alreadyInstalled): ?>
            <div class="logo">
                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="48" fill="#FDB022" stroke="#F59E0B" stroke-width="2"/>
                    <path d="M50 10 L50 50 L75 65 M50 50 L25 65 M50 50 L35 25 M50 50 L65 25" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="50" cy="50" r="6" fill="white"/>
                </svg>
            </div>
            <h1>Вже встановлено!</h1>
            <div class="success-message show">
                <strong>✓ Система вже встановлена</strong><br>
                CMS4Blog готова до використання!
            </div>
            <a href="index.php" style="display: inline-block; padding: 14px 32px; background: #ea580c; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; margin-top: 20px;">
                Перейти на головну
            </a>
        <?php else: ?>
        <div class="logo">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="48" fill="#FDB022" stroke="#F59E0B" stroke-width="2"/>
                <path d="M50 10 L50 50 L75 65 M50 50 L25 65 M50 50 L35 25 M50 50 L65 25" stroke="white" stroke-width="3" stroke-linecap="round"/>
                <circle cx="50" cy="50" r="6" fill="white"/>
            </svg>
        </div>
        
        <h1>Встановлення</h1>
        <p class="subtitle">Введіть параметри бази даних для початку роботи:</p>
        
        <div class="error-message" id="errorMessage"></div>
        
        <div class="success-message" id="successMessage">
            <strong>✓ Успішно встановлено!</strong><br>
            Переадресація на головну сторінку...
        </div>
        
        <form id="installForm">
            <div class="form-group">
                <label for="server">Server</label>
                <input type="text" id="server" name="server" value="localhost" required>
            </div>
            
            <div class="form-group">
                <label for="username">User name</label>
                <input type="text" id="username" name="username" value="root" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="">
            </div>
            
            <div class="form-group">
                <label for="database">Database name</label>
                <input type="text" id="database" name="database" placeholder="my_blog_db" required>
                <div class="hint">Ми створимо цю базу автоматично, якщо її немає.</div>
            </div>
            
            <div class="form-group">
                <label for="admin_password">Admin Password (майбутнє)</label>
                <input type="password" id="admin_password" name="admin_password" placeholder="Придумайте пароль..." required>
            </div>
            
            <div class="submit-group">
                <button type="submit" id="submitBtn">Start blogging</button>
                <span class="keyboard-hint">Ctrl + Enter</span>
            </div>
        </form>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="color: #737373;">Налаштування бази даних...</p>
        </div>
    </div>
    
    <script>
        const form = document.getElementById('installForm');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        const loading = document.getElementById('loading');
        
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.requestSubmit();
            }
        });
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            errorMessage.classList.remove('show');
            successMessage.classList.remove('show');
            
            form.style.display = 'none';
            loading.classList.add('show');
            submitBtn.disabled = true;
            
            const formData = new FormData(form);
            
            try {
                // Відправляємо запит на поточний URL (щоб працювало і в папках)
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                // Перевіряємо, чи повернувся JSON (а не HTML з помилкою PHP)
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Сервер повернув не JSON. Можливо, помилка в коді PHP.");
                }

                const result = await response.json();
                
                loading.classList.remove('show');
                
                if (result.success) {
                    successMessage.classList.add('show');
                    setTimeout(() => {
                        window.location.reload(); // Перезавантаження перекине на index.php
                    }, 1500);
                } else {
                    form.style.display = 'block';
                    submitBtn.disabled = false;
                    errorMessage.textContent = result.error || 'Помилка встановлення';
                    errorMessage.classList.add('show');
                }
            } catch (error) {
                loading.classList.remove('show');
                form.style.display = 'block';
                submitBtn.disabled = false;
                errorMessage.textContent = 'Помилка: ' + error.message;
                errorMessage.classList.add('show');
                console.error(error);
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>