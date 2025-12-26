# CMS4Blog

Легка, швидка та захищена CMS для блогінгу на PHP 8.x + MySQL.

## Статус розробки

🚧 **В процесі розробки**

## Етапи

- [x] Етап 1 — Архітектурний каркас
- [ ] Етап 2 — Інфраструктура (Database, .env, error handling)
- [ ] Етап 3 — Модуль Blog

---

## Архітектурні принципи

### 🎯 Основні принципи

1. **Чіткість і простота** — мінімум абстракцій, максимум ясності
2. **Розділення відповідальностей** — чітка ієрархія: HTTP → Business Logic → Data Access → View
3. **Безпека за замовчуванням** — CSRF-захист, auto-escape, безпечні заголовки
4. **Продуктивність** — DI Container з singleton підтримкою, відсутність зайвих залежностей
5. **Розширюваність** — модульна архітектура з ізольованими компонентами

### ⚡ Вимоги до продуктивності

- Час відгуку основної сторінки: < 100ms
- Використання пам'яті: < 10MB на запит
- Мінімальна кількість файлових операцій
- Підтримка PSR-4 autoloading
- Ледачі завантаження компонентів через DI Container

### 🔒 Вимоги до безпеки

- **CSRF Protection** — токенізація всіх форм
- **XSS Protection** — автоматичне екранування змінних у View
- **SQL Injection** — підготовлені запити (реалізується в Етап 2)
- **Security Headers** — X-Content-Type-Options, X-Frame-Options, CSP
- **Session Security** — HttpOnly, SameSite cookies
- **Input Sanitization** — очищення всіх вхідних даних у Controller

---

## Структура проєкту

```
/public                 # Публічна директорія (document root)
  index.php            # Єдина точка входу
  .htaccess            # Rewrite rules + security headers
  /assets              # Статичні файли (CSS, JS, images)

/app                    # Код застосунку
  /Core                # Ядро фреймворку
    Router.php         # Маршрутизація з підтримкою параметрів
    Container.php      # DI Container з auto-resolve
    Controller.php     # Базовий контролер
    View.php           # Рендеринг шаблонів з auto-escape
    Security.php       # CSRF-захист
  /Contracts           # Інтерфейси для модулів
  /Modules             # Ізольовані функціональні модулі
  /Http                # HTTP-related класи
  /Services            # Сервісний шар

/config                # Конфігураційні файли
/storage               # Файли, що генеруються системою
  /cache               # Кешовані дані
  /logs                # Логи помилок
/templates             # PHP-шаблони View

.env.example           # Приклад конфігурації
.gitignore             # Git ignore rules
```

---

## Компоненти Core

### Router.php

**Можливості:**
- Реєстрація маршрутів: `GET`, `POST`, `PUT`, `DELETE`
- Параметри в URL: `/posts/{id}`, `/users/{username}`
- Групування маршрутів з префіксом
- Middleware support (базова структура)
- Dispatch до `Controller@method`
- 404 handling

**Приклад використання:**
```php
$router->get('/', 'HomeController@index');
$router->get('/posts/{id}', 'PostController@show');

$router->group('/admin', function($router) {
    $router->get('/dashboard', 'AdminController@dashboard');
});
```

### Container.php

**Можливості:**
- Реєстрація сервісів: `bind()`, `singleton()`
- Auto-resolve залежностей через Reflection
- Підтримка callable factories
- Singleton instances

**Приклад використання:**
```php
$container->singleton(Database::class, fn() => new Database($config));
$container->bind(PostService::class);

$service = $container->make(PostService::class);
```

### Controller.php

**Можливості:**
- Доступ до View
- Безпечне отримання Request даних
- Redirect helper
- JSON response helpers
- Автоматична санітизація вхідних даних

**Приклад використання:**
```php
class PostController extends Controller
{
    public function show(string $id): void
    {
        $data = ['title' => 'Post Title', 'content' => 'Content'];
        echo $this->view('posts.show', $data);
    }
}
```

### View.php

**Можливості:**
- Рендеринг PHP-шаблонів
- Auto-escape всіх змінних (XSS protection)
- Layout support
- Передача даних у шаблон

**Приклад використання:**
```php
// В контролері
return $this->view('posts.index', ['posts' => $posts]);

// В шаблоні (templates/posts/index.php)
<?php foreach ($posts as $post): ?>
    <h2><?= $title ?></h2>
<?php endforeach; ?>
```

### Security.php

**Можливості:**
- Генерація CSRF-токенів
- Валідація CSRF-токенів
- Helper-методи для форм
- Безпечні сесії

**Приклад використання:**
```php
// В контролері
$security->requireToken();

// В формі
<?= $security->tokenField() ?>
```

---

## Інструкція з розгортання

### Вимоги

- PHP 8.0 або вище
- Apache/Nginx з mod_rewrite
- Права на запис: `/storage/cache`, `/storage/logs`

### Встановлення

1. **Клонувати репозиторій:**
   ```bash
   git clone https://github.com/Shkrabaliuk/cms4blog.git
   cd cms4blog
   ```

2. **Налаштувати environment:**
   ```bash
   cp .env.example .env
   # Відредагуйте .env відповідно до вашого оточення
   ```

3. **Налаштувати веб-сервер:**
   
   **Apache:**
   - Document root: `/path/to/cms4blog/public`
   - Переконайтесь, що `mod_rewrite` увімкнено

   **Nginx:**
   ```nginx
   server {
       listen 80;
       server_name example.com;
       root /path/to/cms4blog/public;
       
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\. {
           deny all;
       }
   }
   ```

4. **Встановити права доступу:**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 public/assets/
   ```

5. **Перевірити роботу:**
   - Відкрийте браузер: `http://localhost` або ваш домен
   - Ви побачите вітальну сторінку CMS4Blog

### Production Deployment

Для production оточення:

1. Встановіть `APP_ENV=production` і `APP_DEBUG=false` в `.env`
2. Налаштуйте HTTPS та оновіть `APP_URL`
3. Розкоментуйте HSTS заголовок у `.htaccess`
4. Налаштуйте регулярне очищення `/storage/logs`
5. Налаштуйте backup стратегію

---

## Архітектурні обмеження

### ✅ Дозволено

- Strict types у всіх файлах
- Type hints для всіх параметрів та return types
- Namespace structure: `App\Core`, `App\Contracts`, `App\Modules`
- PSR-4 autoloading
- Dependency Injection через Container

### ❌ Заборонено

- Використання сторонніх фреймворків
- Глобальні змінні (крім суперглобальних PHP)
- Бізнес-логіка в Core класах
- Пряма робота з БД у Controller (буде в Етап 2)
- Зайві абстракції без чіткої необхідності

---

## Що далі?

**Етап 2** (Інфраструктура):
- Database abstraction layer
- Query Builder
- Migrations system
- Environment configuration loader
- Improved error handling & logging

**Етап 3** (Модуль Blog):
- Post CRUD operations
- Categories & Tags
- Comments system
- Search functionality
- Admin panel

---

## Ліцензія

MIT