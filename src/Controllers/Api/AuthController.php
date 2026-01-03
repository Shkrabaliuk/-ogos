<?php
namespace App\Controllers\Api;

use App\Services\Auth;

class AuthController
{
    public function login()
    {
        // Only POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Parse input (JSON or Form data)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data) {
            $password = $_POST['password'] ?? '';
        } else {
            $password = $data['password'] ?? '';
        }

        header('Content-Type: application/json');

        if (empty($password)) {
            echo json_encode([
                'success' => false,
                'error' => 'Введіть пароль'
            ]);
            exit;
        }

        // Authenticate
        $result = Auth::login($password);
        echo json_encode($result);
        exit;
    }

    public function logout()
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}
