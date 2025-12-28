<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\InstallController;

/** @var Router $router */

$installedLockFile = STORAGE_PATH . '/installed.lock';
$isInstalled = file_exists($installedLockFile);

if (!$isInstalled) {
    $router->get('/', [InstallController::class, 'index']);
    $router->post('/', [InstallController::class, 'index']);
} else {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/about', [HomeController::class, 'about']);
    $router->get('/contact', [HomeController::class, 'contact']);
}
