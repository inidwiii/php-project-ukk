<?php

namespace Illuminate\Core;

class Container
{
    private $_instances = [];

    public function bind(string $abstract, $concrete = null)
    {
        $this->register($abstract, $concrete, false);
        return $this;
    }

    public function call($concrete, $method, ...$args)
    {
        switch (true) {
            case is_object($concrete):
                break;
            case !is_object($concrete) && is_callable($concrete):
                $concrete = call_user_func($concrete, $this);
                break;
            case array_key_exists($concrete, $this->_instances):
                $concrete = $this->make($concrete);
                break;
        }

        $method = new \ReflectionMethod($concrete, $method);

        return $method->invokeArgs($concrete, $this->resolveDependencies($method, $args));
    }

    public function dump()
    {
        var_dump($this->_instances);
    }

    public function make(string $abstract)
    {
        if (!array_key_exists($abstract, $this->_instances)) {
            throw new \InvalidArgumentException("Can't find abstract '{$abstract}'");
        }

        $current = &$this->_instances[$abstract];

        if ($current['singleton'] && !is_object($current['concrete'])) {
            $current['concrete'] = $this->resolve($current['concrete']);
        }

        if (is_object($current['concrete'])) {
            return $current['concrete'];
        }

        return $this->resolve($current['concrete']);
    }

    public function singleton(string $abstract, $concrete = null)
    {
        $this->register($abstract, $concrete, true);
        return $this;
    }

    private function register(string $abstract, $concrete, bool $singleton)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!is_object($concrete) && is_callable($concrete)) {
            $concrete = call_user_func($concrete, $this);
        }

        $this->_instances[$abstract] = compact('concrete', 'singleton');
    }

    private function resolve($concrete)
    {
        $class = new \ReflectionClass($concrete);
        $dependencies = $this->resolveDependencies($class->getConstructor());

        return $class->newInstanceArgs($dependencies);
    }

    private function resolveDependencies(?\ReflectionMethod $method, array $args = [])
    {
        $resolvedDependencies = [];

        if (!$method instanceof \ReflectionMethod) {
            return $resolvedDependencies;
        }

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType() instanceof \ReflectionNamedType) {
                $resolvedDependencies[] = $this->resolve($parameter->getType()->getName());
                continue;
            }

            if ($parameter->isVariadic()) {
                $resolvedDependencies = array_merge($resolvedDependencies, $args);
                continue;
            }

            $resolvedDependencies[] = array_shift($args) ?? $parameter->getDefaultValue();
        }

        return $resolvedDependencies;
    }
}
