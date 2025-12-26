<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container
{
    private array $bindings = [];
    private array $singletons = [];
    private array $instances = [];

    public function bind(string $abstract, callable|string|null $concrete = null, bool $singleton = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];
    }

    public function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    public function make(string $abstract): mixed
    {
        // Check if singleton instance exists
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get binding or use abstract as concrete
        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;
        $isSingleton = $this->bindings[$abstract]['singleton'] ?? false;

        // Build the instance
        $instance = $this->build($concrete);

        // Store singleton instance
        if ($isSingleton) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    private function build(mixed $concrete): mixed
    {
        // If concrete is a callable, execute it
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        // Use reflection to auto-resolve dependencies
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new \RuntimeException("Target class [{$concrete}] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new \RuntimeException("Target [{$concrete}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        // No constructor means no dependencies
        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = $constructor->getParameters();

        // Resolve all dependencies
        $instances = $this->resolveDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->resolveParameter($parameter);
            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // No type hint
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new \RuntimeException("Cannot resolve parameter [{$parameter->getName()}]");
        }

        // For built-in types, use default value if available
        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new \RuntimeException("Cannot resolve built-in parameter [{$parameter->getName()}]");
        }

        // Resolve class dependency
        $className = $type->getName();

        try {
            return $this->make($className);
        } catch (\Exception $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new \RuntimeException("Cannot resolve class dependency [{$className}]", 0, $e);
        }
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
}
