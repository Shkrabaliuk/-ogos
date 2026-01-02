<div class="login-container">
    <div class="login-box">
        <h1 class="login-title">/\ogos</h1>
        <p class="login-subtitle">Адміністрування</p>
        
        <?php if ($error): ?>
            <div class="login-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Ім'я користувача</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autofocus
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-unlock"></i>
                Увійти
            </button>
        </form>
        
        <div class="login-footer">
            <a href="/" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Повернутися на головну
            </a>
        </div>
    </div>
</div>
