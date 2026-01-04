<?php
namespace App\Controllers\Admin;

use App\Services\Auth;

class ReinstallController
{
    public function execute()
    {
        Auth::require();

        // Delete config file
        $configFile = __DIR__ . '/../../../src/Config/db.php';
        if (file_exists($configFile)) {
            unlink($configFile);
        }

        // Redirect to installer
        header('Location: /install.php');
        exit;
    }
}
