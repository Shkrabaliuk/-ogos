<?php
// install.php

// 1. AJAX: ОТРИМАННЯ СПИСКУ БАЗ ДАНИХ
if (isset($_POST['action']) && $_POST['action'] === 'get_databases') {
    header('Content-Type: application/json');
    
    $server = $_POST['server'] ?? 'localhost';
    $user = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';

    try {
        // Підключаємося без вибору конкретної бази
        $dsn = "mysql:host=$server;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        // Отримуємо список баз
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Фільтруємо системні бази (їх краще не чіпати)
        $databases = array_diff($databases, ['information_schema', 'mysql', 'performance_schema', 'sys']);
        
        echo json_encode(['success' => true, 'databases' => array_values($databases)]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// 2. ОБРОБКА ВСТАНОВЛЕННЯ (як і раніше)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $server = $_POST['server'] ?? 'localhost';
    $user = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $database = $_POST['database'] ?? '';
    
    try {
        $dsn = "mysql:host=$server;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        // Створюємо базу, якщо це нова назва
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
        $pdo->exec("USE `$database`");
        
        $sql = "CREATE TABLE IF NOT EXISTS `posts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `content` text NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);

        $configContent = "<?php
define('DB_HOST', '$server');
define('DB_NAME', '$database');
define('DB_USER', '$user');
define('DB_PASS', '$password');
";
        if (file_put_contents('config.php', $configContent) === false) {
             throw new Exception("Не вдалося створити config.php");
        }
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
$alreadyInstalled = file_exists('config.php');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення</title>
    <link rel="stylesheet" href="../assets/css/install.css">
</head>
<body>
    <div class="container">
        <?php if ($alreadyInstalled): ?>
            <h1>Вже встановлено</h1>
            <p>Блог налаштований. Видаліть файл <code>config.php</code>, якщо хочете почати заново.</p>
            <a href="../index.php"><button>Перейти до блогу</button></a>
        <?php else: ?>
            
            <h1>Встановлення</h1>
            <p class="subtitle">Введіть параметри для підключення до MySQL:</p>
            
            <div class="error-message" id="errorMessage"></div>
            <div class="success-message" id="successMessage">✓ Успішно! Переходимо на сайт...</div>
            
            <form id="installForm">
                <div class="form-group">
                    <label>Сервер (Host)</label>
                    <input type="text" name="server" id="server" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label>Користувач та пароль БД</label>
                    <div class="double-input">
                        <input type="text" name="username" id="username" placeholder="root" required>
                        <input type="password" name="password" id="password" placeholder="Пароль">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ім'я бази даних</label>
                    <input type="text" name="database" id="database" list="dbList" placeholder="my_blog" autocomplete="off" required>
                    <datalist id="dbList"></datalist>
                    
                    <div class="hint" id="dbHint">Натисніть сюди, щоб завантажити список існуючих баз, або введіть нову.</div>
                </div>
                
                <div class="form-group" style="margin-top: 40px;">
                    <label>Створіть пароль адміністратора:</label>
                    <input type="password" name="admin_password" required>
                </div>
                
                <button type="submit" id="submitBtn">Почати блог</button>
                <span class="keyboard-hint">Ctrl + Enter</span>
            </form>
            
            <div class="loading" id="loading">Працюємо...</div>
        <?php endif; ?>
    </div>

    <script>
        const form = document.getElementById('installForm');
        
        if(form) {
            const dbInput = document.getElementById('database');
            const dbList = document.getElementById('dbList');
            const dbHint = document.getElementById('dbHint');
            const serverInput = document.getElementById('server');
            const userInput = document.getElementById('username');
            const passInput = document.getElementById('password');

            // --- ФУНКЦІЯ ЗАВАНТАЖЕННЯ БАЗ ---
            async function loadDatabases() {
                // Не вантажимо, якщо немає логіна
                if (!userInput.value) return;

                dbHint.textContent = "Завантаження списку баз...";
                
                const formData = new FormData();
                formData.append('action', 'get_databases');
                formData.append('server', serverInput.value);
                formData.append('username', userInput.value);
                formData.append('password', passInput.value);

                try {
                    const response = await fetch(window.location.href, { method: 'POST', body: formData });
                    const result = await response.json();

                    if (result.success) {
                        dbList.innerHTML = ''; // Очищаємо старі
                        result.databases.forEach(db => {
                            const option = document.createElement('option');
                            option.value = db;
                            dbList.appendChild(option);
                        });
                        dbHint.textContent = "Виберіть зі списку або введіть нову назву для створення.";
                        dbHint.style.color = "#00a000";
                    } else {
                        dbHint.textContent = "Не вдалося отримати список (перевірте пароль). Але ви можете ввести назву вручну.";
                        dbHint.style.color = "#d00";
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            // Завантажуємо бази, коли користувач клікає на поле "База даних"
            dbInput.addEventListener('focus', loadDatabases);


            // --- ЛОГІКА ВІДПРАВКИ ФОРМИ (ЯК І РАНІШЕ) ---
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
                loading.classList.add('show');
                submitBtn.disabled = true;
                
                const formData = new FormData(form);
                try {
                    const response = await fetch(window.location.href, { method: 'POST', body: formData });
                    const result = await response.json();
                    
                    loading.classList.remove('show');
                    if (result.success) {
                        successMessage.classList.add('show');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        submitBtn.disabled = false;
                        errorMessage.textContent = result.error;
                        errorMessage.classList.add('show');
                    }
                } catch (error) {
                    loading.classList.remove('show');
                    submitBtn.disabled = false;
                    errorMessage.textContent = 'Connection error';
                    errorMessage.classList.add('show');
                }
            });
        }
    </script>
</body>
</html>