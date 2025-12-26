<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function view(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        // Sanitize input from request
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        
        if (is_string($value)) {
            return $this->sanitizeString($value);
        }
        
        if (is_array($value)) {
            return $this->sanitizeArray($value);
        }
        
        return $value;
    }

    protected function all(): array
    {
        $data = array_merge($_GET, $_POST);
        return $this->sanitizeArray($data);
    }

    protected function redirect(string $path, int $statusCode = 302): void
    {
        header("Location: {$path}", true, $statusCode);
        exit;
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonError(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }

    protected function jsonSuccess(string $message, mixed $data = null, int $statusCode = 200): void
    {
        $response = ['success' => true, 'message' => $message];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->json($response, $statusCode);
    }

    private function sanitizeString(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function sanitizeArray(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}
