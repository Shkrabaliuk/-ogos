<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення - CMS4Blog</title>
    <link rel="stylesheet" href="/assets/css/install.css">
</head>
<body>
    <div class="container">
        <?php if (isset($alreadyInstalled) && $alreadyInstalled): ?>
            <!-- Вже встановлено -->
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
            <a href="/" style="display: inline-block; padding: 14px 32px; background: #ea580c; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; margin-top: 20px;">
                Перейти на головну
            </a>
        <?php else: ?>
        <!-- Форма встановлення -->
        <div class="logo">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="48" fill="#FDB022" stroke="#F59E0B" stroke-width="2"/>
                <path d="M50 10 L50 50 L75 65 M50 50 L25 65 M50 50 L35 25 M50 50 L65 25" stroke="white" stroke-width="3" stroke-linecap="round"/>
                <circle cx="50" cy="50" r="6" fill="white"/>
            </svg>
        </div>
        
        <!-- Заголовок -->
        <h1>Встановлення</h1>
        <p class="subtitle">Database parameters that your hosting provider has given you:</p>
        
        <!-- Повідомлення про помилки -->
        <div class="error-message" id="errorMessage"></div>
        
        <!-- Повідомлення про успіх -->
        <div class="success-message" id="successMessage">
            <strong>✓ Успішно встановлено!</strong><br>
            Переадресація на головну сторінку...
        </div>
        
        <!-- Форма -->
        <form id="installForm">
            <!-- Server -->
            <div class="form-group">
                <label for="server">Server</label>
                <input type="text" id="server" name="server" value="localhost" required>
            </div>
            
            <!-- User name and password -->
            <div class="form-group">
                <label for="username">User name and password</label>
                <input type="text" id="username" name="username" value="root" required>
                <input type="password" id="password" name="password" placeholder="" style="margin-top: 8px;">
            </div>
            
            <!-- Database name -->
            <div class="form-group">
                <label for="database">Database name</label>
                <input type="text" id="database" name="database" placeholder="" required>
                <div class="hint">Ask your hosting provider how to create database, if necessary</div>
            </div>
            
            <!-- Admin password -->
            <div class="form-group">
                <label for="admin_password">Password you'd like to use to access your blog:</label>
                <input type="password" id="admin_password" name="admin_password" placeholder="" required>
            </div>
            
            <!-- Submit -->
            <div class="submit-group">
                <button type="submit" id="submitBtn">Start blogging</button>
                <span class="keyboard-hint">Ctrl + Enter</span>
            </div>
        </form>
        
        <!-- Loading -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="color: #737373;">Встановлення системи...</p>
        </div>
    </div>
    
    <script>
        const form = document.getElementById('installForm');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        const loading = document.getElementById('loading');
        
        // Keyboard shortcut Ctrl + Enter
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.requestSubmit();
            }
        });
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Приховуємо повідомлення
            errorMessage.classList.remove('show');
            successMessage.classList.remove('show');
            
            // Показуємо loading
            form.style.display = 'none';
            loading.classList.add('show');
            submitBtn.disabled = true;
            
            // Збираємо дані
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                loading.classList.remove('show');
                
                if (result.success) {
                    successMessage.classList.add('show');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
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
                errorMessage.textContent = 'Помилка підключення до сервера: ' + error.message;
                errorMessage.classList.add('show');
            }
        });
    </script>
        <?php endif; ?>
    </div>
</body>
</html>
