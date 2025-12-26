<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $prefix = '';

    public function get(string $path, string|callable $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string|callable $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string|callable $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string|callable $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $this->prefix = $previousPrefix . $prefix;
        
        $callback($this);
        
        $this->prefix = $previousPrefix;
    }

    public function middleware(string|array $middleware): self
    {
        $this->middlewares = is_array($middleware) ? $middleware : [$middleware];
        return $this;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                $params = $this->extractParams($route['path'], $matches);
                
                return $this->executeHandler($route['handler'], $params, $route['middlewares']);
            }
        }

        $this->handleNotFound();
    }

    private function addRoute(string $method, string $path, string|callable $handler): self
    {
        $fullPath = $this->prefix . $path;
        
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middlewares' => $this->middlewares
        ];

        $this->middlewares = [];
        
        return $this;
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function extractParams(string $path, array $matches): array
    {
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $path, $paramNames);
        
        $params = [];
        foreach ($paramNames[1] as $index => $name) {
            if (isset($matches[$index])) {
                $params[$name] = $matches[$index];
            }
        }
        
        return $params;
    }

    private function executeHandler(string|callable $handler, array $params, array $middlewares): mixed
    {
        // For future middleware support
        foreach ($middlewares as $middleware) {
            // Middleware execution can be implemented here
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler);
            
            if (!class_exists($controller)) {
                throw new \RuntimeException("Controller {$controller} not found");
            }

            $instance = new $controller();
            
            if (!method_exists($instance, $method)) {
                throw new \RuntimeException("Method {$method} not found in {$controller}");
            }

            return call_user_func_array([$instance, $method], $params);
        }

        throw new \RuntimeException('Invalid route handler');
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
        exit;
    }
}
