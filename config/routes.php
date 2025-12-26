<?php

declare(strict_types=1);

/**
 * Routes Configuration
 * 
 * Define all application routes here.
 */

use App\Core\Router;

/** @var Router $router */

// Example routes (remove in production)
$router->get('/', function () {
    echo '<h1>Welcome to CMS4Blog!</h1>';
    echo '<p>Your lightweight PHP CMS is ready.</p>';
});

// Example: Group routes with prefix
// $router->group('/api', function (Router $router) {
//     $router->get('/posts', [PostController::class, 'index']);
//     $router->get('/posts/{id}', [PostController::class, 'show']);
//     $router->post('/posts', [PostController::class, 'store']);
// });

// Example: Admin routes with middleware
// $router->group('/admin', function (Router $router) {
//     $router->get('/dashboard', [AdminController::class, 'dashboard']);
// }, ['AuthMiddleware']);