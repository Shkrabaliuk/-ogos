<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $groupPrefix = '';
    private array $groupMiddlewares = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    public function group(string $prefix, callable $callback, array $middlewares = []): self
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddlewares = $this->groupMiddlewares;

        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddlewares = array_merge($previousMiddlewares, $middlewares);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddlewares = $previousMiddlewares;

        return $this;
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares): self
    {
        $fullPath = $this->groupPrefix . $path;
        $allMiddlewares = array_merge($this->groupMiddlewares, $middlewares);

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middlewares' => $allMiddlewares,
            'pattern' => $this->buildPattern($fullPath),
        ];

        return $this;
    }

    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '', '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->executeRoute($route, $params);
            }
        }

        return $this->handleNotFound();
    }

    private function executeRoute(array $route, array $params): mixed
    {
        $handler = $route['handler'];

        // Execute middlewares
        foreach ($route['middlewares'] as $middleware) {
            $result = $this->executeMiddleware($middleware);
            if ($result !== null) {
                return $result;
            }
        }

        if (is_callable($handler)) {
            return $handler($params);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            return $controller->$method($params);
        }

        throw new \RuntimeException('Invalid route handler');
    }

    private function executeMiddleware(string $middleware): mixed
    {
        if (class_exists($middleware)) {
            $instance = new $middleware();
            if (method_exists($instance, 'handle')) {
                return $instance->handle();
            }
        }
        return null;
    }

    private function handleNotFound(): never
    {
        http_response_code(404);
        echo '404 Not Found';
        exit;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
