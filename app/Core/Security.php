<?php

declare(strict_types=1);

namespace App\Core;

class Security
{
    private const TOKEN_LENGTH = 32;
    private const TOKEN_KEY = '_csrf_token';

    public function __construct()
    {
        $this->startSession();
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::TOKEN_KEY] = $token;
        
        return $token;
    }

    public function getToken(): ?string
    {
        return $_SESSION[self::TOKEN_KEY] ?? null;
    }

    public function validateToken(string $token): bool
    {
        $sessionToken = $this->getToken();

        if ($sessionToken === null) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public function verifyToken(): bool
    {
        $token = $_POST[self::TOKEN_KEY] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if ($token === null) {
            return false;
        }

        return $this->validateToken($token);
    }

    public function requireToken(): void
    {
        if (!$this->verifyToken()) {
            http_response_code(403);
            echo 'CSRF token validation failed';
            exit;
        }
    }

    public function tokenField(): string
    {
        $token = $this->getToken() ?? $this->generateToken();
        
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars(self::TOKEN_KEY, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    public function tokenMeta(): string
    {
        $token = $this->getToken() ?? $this->generateToken();
        
        return sprintf(
            '<meta name="csrf-token" content="%s">',
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
                'use_strict_mode' => true,
            ]);
        }
    }

    public function regenerateSession(): void
    {
        session_regenerate_id(true);
        $this->generateToken();
    }
}
