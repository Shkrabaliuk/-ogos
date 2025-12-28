<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple Router for handling HTTP requests
 */
class Router
{
    private array $routes = [];

    /**
     * Add GET route
     */
    public function get(string $path, callable|array $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    /**
     * Add POST route
     */
    public function post(string $path, callable|array $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    /**
     * Dispatch request to appropriate handler
     */
    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $path => $handler) {
            if ($path === $uri) {
                $this->executeHandler($handler);
                return;
            }
        }

        $this->handleNotFound();
    }

    /**
     * Execute route handler
     */
    private function executeHandler(callable|array $handler): void
    {
        if (is_callable($handler)) {
            call_user_func($handler);
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            call_user_func([$controller, $method]);
        }
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
    }
}
