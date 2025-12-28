<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class InstallController extends Controller
{
    private const LOCK_FILE = STORAGE_PATH . '/installed.lock';

    public function index(): void
    {
        if ($this->isPost()) {
            $this->processInstall();
            return;
        }

        $this->showInstallForm();
    }

    private function showInstallForm(): void
    {
        $alreadyInstalled = $this->isInstalled();
        echo $this->render('install/simple', [
            'alreadyInstalled' => $alreadyInstalled
        ]);
    }

    private function processInstall(): void
    {
        $server = $this->inputPost('server', 'localhost');
        $username = $this->inputPost('username', 'root');
        $password = $this->inputPost('password', '');
        $database = $this->inputPost('database');
        $adminPassword = $this->inputPost('admin_password');

        if (empty($database) || empty($adminPassword)) {
            $this->json(['success' => false, 'error' => 'Database name and admin password are required'], 400);
        }

        file_put_contents(self::LOCK_FILE, date('Y-m-d H:i:s'));
        $this->json(['success' => true, 'message' => 'Installation completed successfully']);
    }

    private function isInstalled(): bool
    {
        return file_exists(self::LOCK_FILE);
    }
}
