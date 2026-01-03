<?php
// index.php - Main Entry Point

// 1. Check for Installation
if (!file_exists(__DIR__ . '/src/Config/db.php')) {
    if (file_exists(__DIR__ . '/install.php')) {
        header('Location: /install.php');
        exit;
    } else {
        die('System is not installed and install.php is missing.');
    }
}

// 2. Composer Autoload
require __DIR__ . '/vendor/autoload.php';

use Bramus\Router\Router;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\RssController;
use App\Controllers\SitemapController;
use App\Controllers\SearchController;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\CommentController;

// 3. Initialize Router
$router = new Router();

// 4. Define Routes
// Home
$router->get('/', function () {
    $controller = new HomeController();
    $controller->index();
});

// Single Post View
// Matches alphanumeric slugs + dashes
$router->get('/post/([a-z0-9\-]+)', function ($slug) {
    $controller = new PostController();
    $controller->show($slug);
});

// RSS Feed
$router->get('/rss.php', function () {
    $controller = new RssController();
    $controller->index();
});

// Sitemap
$router->get('/sitemap.php', function () {
    $controller = new SitemapController();
    $controller->index();
});

// Search
$router->get('/search.php', function () {
    $controller = new SearchController();
    $controller->index();
});

// API Routes
$router->post('/api/login.php', function () {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/api/logout.php', function () {
    $controller = new AuthController();
    $controller->logout();
});

$router->post('/api/post_comment.php', function () {
    $controller = new CommentController();
    $controller->store();
});

// Legacy/Root fallback for post slugs (e.g. /my-post-title)
// CAUTION: This might conflict with other root files if not careful.
// Placing it last acts as a catch-all for root level slugs.
$router->get('/([a-z0-9\-]+)', function ($slug) {
    // Exclude physical files or specific reserved words if needed check logic inside controller or here
    $controller = new PostController();
    $controller->show($slug);
});

// Admin Routes Group
$router->mount('/admin', function () use ($router) {
    // Dashboard (redirect to settings for now)
    $router->get('/', function () {
        header('Location: /admin/settings');
    });

    // Settings
    $router->get('/settings', function () {
        $controller = new \App\Controllers\Admin\SettingsController();
        $controller->index();
    });

    $router->post('/settings', function () {
        $controller = new \App\Controllers\Admin\SettingsController();
        $controller->update();
    });

    // Post Management
    $router->post('/save_post', function () {
        $controller = new \App\Controllers\Admin\PostController();
        $controller->save();
    });

    $router->post('/delete_post', function () {
        $controller = new \App\Controllers\Admin\PostController();
        $controller->delete();
    });

    // Utilities
    $router->get('/backup', function () {
        $controller = new \App\Controllers\Admin\BackupController();
        $controller->download();
    });

    $router->get('/clear_logs', function () {
        $controller = new \App\Controllers\Admin\LogsController();
        $controller->clear();
    });

    $router->post('/upload_image', function () {
        $controller = new \App\Controllers\Admin\MediaController();
        $controller->upload();
    });

    // Legacy fallback for any remaining old admin files
    $router->get('/(.*)', function ($file) {
        $path = __DIR__ . '/src/admin/' . $file;
        if (file_exists($path)) {
            require $path;
        } else {
            echo "Admin page not found.";
        }
    });
});

// 5. Run Application
$router->run();