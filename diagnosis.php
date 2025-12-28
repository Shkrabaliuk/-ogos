<?php
/**
 * Діагностичний скрипт для перевірки проблем з логіном
 * Розмістіть в корені сайту та відкрийте через браузер
 */

// Запускаємо сесію
session_start();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Діагностика логіну CMS4Blog</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: #0a0; font-weight: bold; }
        .error { color: #d00; font-weight: bold; }
        .warning { color: #f80; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: bold; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        code { 
            background: #f0f0f0; 
            padding: 2px 6px; 
            border-radius: 3px; 
            font-family: monospace;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 15px 0;
        }
        .test-section {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>🔍 Діагностика проблем з логіном</h1>
    
    <div class="card">
        <h2>1️⃣ Перевірка файлів</h2>
        <table>
            <tr>
                <th>Файл</th>
                <th>Статус</th>
                <th>Шлях</th>
            </tr>
            <?php
            $files_to_check = [
                'config.php' => __DIR__ . '/config.php',
                'includes/db.php' => __DIR__ . '/includes/db.php',
                'includes/functions.php' => __DIR__ . '/includes/functions.php',
                'admin/login.php' => __DIR__ . '/admin/login.php',
                'admin/admin.php' => __DIR__ . '/admin/admin.php',
            ];
            
            foreach ($files_to_check as $name => $path) {
                $exists = file_exists($path);
                $readable = $exists ? is_readable($path) : false;
                echo "<tr>";
                echo "<td><code>$name</code></td>";
                echo "<td>";
                if ($exists && $readable) {
                    echo '<span class="success">✓ OK</span>';
                } elseif ($exists) {
                    echo '<span class="warning">⚠ Існує, але не читається</span>';
                } else {
                    echo '<span class="error">✗ Не знайдено</span>';
                }
                echo "</td>";
                echo "<td><small>$path</small></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div class="card">
        <h2>2️⃣ Перевірка бази даних</h2>
        <?php
        if (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
            
            echo "<table>";
            echo "<tr><th>Параметр</th><th>Значення</th></tr>";
            echo "<tr><td>DB_HOST</td><td>" . (defined('DB_HOST') ? DB_HOST : '<span class="error">Не визначено</span>') . "</td></tr>";
            echo "<tr><td>DB_NAME</td><td>" . (defined('DB_NAME') ? DB_NAME : '<span class="error">Не визначено</span>') . "</td></tr>";
            echo "<tr><td>DB_USER</td><td>" . (defined('DB_USER') ? DB_USER : '<span class="error">Не визначено</span>') . "</td></tr>";
            echo "<tr><td>DB_PASS</td><td>" . (defined('DB_PASS') ? (DB_PASS ? '••••••' : '<span class="warning">Порожній</span>') : '<span class="error">Не визначено</span>') . "</td></tr>";
            echo "</table>";
            
            // Спроба підключення до БД
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                echo '<div class="info-box"><span class="success">✓</span> Підключення до бази даних успішне!</div>';
                
                // Перевірка таблиць
                echo "<h3>Таблиці в базі даних:</h3>";
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo "<table>";
                    echo "<tr><th>Таблиця</th><th>Кількість записів</th></tr>";
                    foreach ($tables as $table) {
                        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                        echo "<tr><td><code>$table</code></td><td>$count</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo '<p class="warning">⚠ База даних порожня! Запустіть інсталятор.</p>';
                }
                
                // Перевірка таблиці users
                if (in_array('users', $tables)) {
                    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    echo "<div class='info-box'>";
                    if ($userCount > 0) {
                        echo "<span class='success'>✓</span> Таблиця <code>users</code> існує, користувачів: $userCount<br>";
                        
                        // Показуємо структуру таблиці users
                        $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC);
                        echo "<br><strong>Структура таблиці users:</strong><br>";
                        echo "<table style='margin-top: 10px;'>";
                        echo "<tr><th>Поле</th><th>Тип</th></tr>";
                        foreach ($columns as $col) {
                            echo "<tr><td><code>{$col['Field']}</code></td><td>{$col['Type']}</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<span class='warning'>⚠</span> Таблиця <code>users</code> порожня. Потрібно зареєструвати першого адміна.";
                    }
                    echo "</div>";
                } else {
                    echo '<p class="error">✗ Таблиця <code>users</code> не існує! Вона має створитися автоматично при першому вході.</p>';
                }
                
                // Перевірка таблиці posts
                if (in_array('posts', $tables)) {
                    $postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
                    echo "<div class='info-box'><span class='success'>✓</span> Таблиця <code>posts</code> існує, постів: $postCount</div>";
                }
                
            } catch (PDOException $e) {
                echo '<div class="test-section"><span class="error">✗</span> Помилка підключення до БД:<br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre></div>';
            }
        } else {
            echo '<p class="error">✗ Файл config.php не знайдено! Запустіть інсталятор.</p>';
        }
        ?>
    </div>

    <div class="card">
        <h2>3️⃣ Перевірка сесій</h2>
        <?php
        echo "<table>";
        echo "<tr><th>Параметр</th><th>Значення</th></tr>";
        echo "<tr><td>session.save_path</td><td><code>" . session_save_path() . "</code></td></tr>";
        echo "<tr><td>session.save_handler</td><td><code>" . ini_get('session.save_handler') . "</code></td></tr>";
        echo "<tr><td>session_id()</td><td><code>" . session_id() . "</code></td></tr>";
        echo "<tr><td>Session працює?</td><td>";
        
        // Тест сесії
        $_SESSION['test'] = 'works';
        if (isset($_SESSION['test']) && $_SESSION['test'] === 'works') {
            echo '<span class="success">✓ Так</span>';
        } else {
            echo '<span class="error">✗ Ні</span>';
        }
        echo "</td></tr>";
        
        echo "<tr><td>is_admin() в сесії</td><td>";
        if (isset($_SESSION['is_admin'])) {
            echo '<span class="success">✓ Так (' . ($_SESSION['is_admin'] ? 'TRUE' : 'FALSE') . ')</span>';
        } else {
            echo '<span class="warning">⚠ Не встановлено</span>';
        }
        echo "</td></tr>";
        
        echo "</table>";
        
        // Показуємо всю сесію
        if (!empty($_SESSION)) {
            echo "<h3>Вміст сесії:</h3>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        }
        ?>
    </div>

    <div class="card">
        <h2>4️⃣ Перевірка PHP налаштувань</h2>
        <table>
            <tr>
                <th>Параметр</th>
                <th>Значення</th>
            </tr>
            <tr>
                <td>PHP версія</td>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <td>PDO MySQL</td>
                <td><?= extension_loaded('pdo_mysql') ? '<span class="success">✓ Увімкнено</span>' : '<span class="error">✗ Вимкнено</span>' ?></td>
            </tr>
            <tr>
                <td>Session extension</td>
                <td><?= extension_loaded('session') ? '<span class="success">✓ Увімкнено</span>' : '<span class="error">✗ Вимкнено</span>' ?></td>
            </tr>
            <tr>
                <td>display_errors</td>
                <td><?= ini_get('display_errors') ? 'On' : 'Off' ?></td>
            </tr>
            <tr>
                <td>error_reporting</td>
                <td><?= error_reporting() ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>5️⃣ Тест логіну (симуляція)</h2>
        
        <div class="test-section">
            <p><strong>Для тестування логіну:</strong></p>
            <ol>
                <li>Відкрийте <a href="/admin/login.php" target="_blank">/admin/login.php</a></li>
                <li>Введіть пароль</li>
                <li>Якщо помилка - перегляньте логи нижче</li>
            </ol>
        </div>

        <?php
        // Перевіряємо чи є POST запит (тестовий логін)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_password'])) {
            echo "<h3>Результат тесту:</h3>";
            
            $password = $_POST['test_password'];
            
            if (file_exists(__DIR__ . '/config.php') && isset($pdo)) {
                try {
                    // Створюємо таблицю якщо її немає
                    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `password` varchar(255) NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                    
                    $adminExists = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    
                    if (!$adminExists) {
                        // Реєстрація
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (password) VALUES (?)");
                        $stmt->execute([$hash]);
                        
                        $_SESSION['is_admin'] = true;
                        echo '<div class="info-box"><span class="success">✓</span> Адміна створено! Пароль збережено.</div>';
                        echo '<p>Тепер спробуйте увійти через <a href="/admin/login.php">/admin/login.php</a> з цим паролем.</p>';
                    } else {
                        // Перевірка
                        $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
                        $user = $stmt->fetch();
                        
                        if ($user && password_verify($password, $user['password'])) {
                            $_SESSION['is_admin'] = true;
                            echo '<div class="info-box"><span class="success">✓</span> Пароль правильний! Сесія встановлена.</div>';
                            echo '<p>Тепер відкрийте <a href="/admin/admin.php">/admin/admin.php</a></p>';
                        } else {
                            echo '<div class="test-section"><span class="error">✗</span> Невірний пароль!</div>';
                            echo '<p>Існуючий хеш: <code>' . htmlspecialchars($user['password'] ?? 'немає') . '</code></p>';
                        }
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="test-section"><span class="error">✗</span> Помилка: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<p class="error">Немає підключення до БД</p>';
            }
        }
        ?>

        <form method="POST" style="margin-top: 20px;">
            <p><strong>Спробуйте тестовий логін тут:</strong></p>
            <input type="password" name="test_password" placeholder="Введіть пароль" required style="padding: 10px; width: 300px; font-size: 16px;">
            <button type="submit" style="padding: 10px 20px; background: #2196F3; color: white; border: none; cursor: pointer; font-size: 16px;">Тест логіну</button>
        </form>
    </div>

    <div class="card">
        <h2>📋 Рекомендації</h2>
        
        <?php if (!isset($pdo)): ?>
            <div class="test-section">
                <strong>⚠ Критично:</strong> Немає підключення до БД<br>
                Запустіть інсталятор: <a href="/install/install.php">/install/install.php</a>
            </div>
        <?php elseif (isset($userCount) && $userCount == 0): ?>
            <div class="info-box">
                <strong>💡 Підказка:</strong> Користувачів немає в БД<br>
                Просто відкрийте <a href="/admin/login.php">/admin/login.php</a> та створіть пароль (перший запуск)
            </div>
        <?php else: ?>
            <div class="info-box">
                <strong>✓ Все виглядає нормально!</strong><br>
                Спробуйте увійти через <a href="/admin/login.php">/admin/login.php</a>
            </div>
        <?php endif; ?>
        
        <h3>Типові проблеми та рішення:</h3>
        <ol>
            <li><strong>Не можу увійти / не перенаправляє</strong>
                <ul>
                    <li>Перевірте чи працюють сесії (вище має бути ✓)</li>
                    <li>Очистіть cookies браузера</li>
                    <li>Спробуйте інший браузер</li>
                </ul>
            </li>
            <li><strong>Помилка "Call to undefined function"</strong>
                <ul>
                    <li>Перевірте чи всі файли includes завантажені</li>
                    <li>Перевірте права доступу до файлів (644)</li>
                </ul>
            </li>
            <li><strong>Пароль не приймається</strong>
                <ul>
                    <li>Можливо БД users порожня - спробуйте тест вище</li>
                    <li>Перевірте чи не було змінено таблицю users вручну</li>
                </ul>
            </li>
        </ol>
    </div>

    <div class="card" style="background: #f9f9f9;">
        <p style="text-align: center; color: #666;">
            <small>Після виправлення проблеми видаліть цей файл: <code>rm diagnosis.php</code></small>
        </p>
    </div>

</body>
</html>
