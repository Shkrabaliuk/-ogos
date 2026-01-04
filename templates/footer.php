</main>

<footer>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 0;">
            © <span><?= htmlspecialchars($blogSettings['blog_author'] ?? 'Автор') ?></span>, <?= date('Y') ?>
        </p>

        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <?php if (!\App\Services\Auth::check()): ?>
                <a href="#" id="loginToggle" class="icon-link" title="Вхід" style="color: #999;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <a href="/rss.php" class="icon-link" title="RSS Feed" style="color: #666;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 11a9 9 0 0 1 9 9"></path>
                    <path d="M4 4a16 16 0 0 1 16 16"></path>
                    <circle cx="5" cy="19" r="1"></circle>
                </svg>
            </a>
        </div>
    </div>
</footer>

<!-- Login Modal -->
<style>
    dialog#loginDialog {
        margin: auto;
        padding: 0;
        border: none;
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-width: 400px;
        width: 90%;
        overflow: hidden;
        background: var(--bg-color, #fff);
        color: var(--text-color, #333);
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    dialog#loginDialog[open] {
        opacity: 1;
        transform: scale(1);
    }

    dialog#loginDialog::backdrop {
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(2px);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color, #eee);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        color: #999;
        line-height: 1;
    }
    .close-btn:hover { color: #333; }

    .modal-body {
        padding: 1.5rem;
    }

    #loginForm label {
        display: block;
        margin-bottom: 1rem;
    }
    
    #loginForm input {
        width: 100%;
        padding: 0.5rem;
        margin-top: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    #loginForm button {
        width: 100%;
        padding: 0.75rem;
        background: var(--accent, #007bff);
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
    }
    
    #loginForm button:hover {
        opacity: 0.9;
    }
</style>

<dialog id="loginDialog">
    <div class="modal-header">
        <h3>Вхід для адміністратора</h3>
        <form method="dialog">
            <button class="close-btn">✕</button>
        </form>
    </div>
    
    <div class="modal-body">
        <form id="loginForm">
            <label>
                Пароль:
                <input type="password" name="password" required autocomplete="current-password" autofocus>
            </label>
            <button type="submit">Увійти</button>
        </form>
        <p id="loginError" style="color: #d00; margin-top: 10px; font-size: 0.9rem; text-align: center;" hidden></p>
    </div>
</dialog>

<script src="/assets/js/libs/fotorama/fotorama.js"></script>
<script src="/assets/js/libs/momentjs/moment-with-locales.min.js"></script>

<script>
    // Moment.js locale
    if (typeof moment !== 'undefined') {
        moment.locale('uk');
    }

    // Login Dialog Logic
    const loginDialog = document.getElementById('loginDialog');
    const loginToggle = document.getElementById('loginToggle');
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');

    if (loginToggle && loginDialog) {
        loginToggle.addEventListener('click', (e) => {
            e.preventDefault();
            loginDialog.showModal();
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);

            fetch('/api/login.php', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        loginError.textContent = data.error || 'Помилка входу';
                        loginError.hidden = false;
                    }
                })
                .catch(() => {
                    loginError.textContent = 'Помилка з\'єднання';
                    loginError.hidden = false;
                });
        });
    }
</script>

</body>

</html>