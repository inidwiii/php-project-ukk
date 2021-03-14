<?php

namespace Illuminate\Core;

class Container
{
    /**
     * Hold all the instances registered
     * @var array $_instances
     */
    private $_instances = [];

    /**
     * Registering a new instance object into the container
     * @var string $abstract
     * @var callable|null|object|string $concrete
     * @return \Illuminate\Core\Container|self
     */
    public function bind($abstract, $concrete = null)
    {
        $this->register($abstract, $concrete, false);
        return $this;
    }

    /**
     * Call to a method from the registered instance
     * @param string $abstract
     * @param string $method
     * @param mixed[] ...$args
     * @return mixed
     * @throws \InvalidArgumentException|\ReflectionException
     */
    public function call($abstract, $method, ...$args)
    {
        if (!array_key_exists($abstract, $this->_instances)) {
            throw new \InvalidArgumentException("Can't find abstract with name '{$abstract}'");
        }

        $concrete = $this->make($abstract);
        $method = new \ReflectionMethod($concrete, $method);

        return $method->invokeArgs($concrete, $this->resolveDependencies($method, $args));
    }

    public function dump()
    {
        var_dump($this->_instances);
    }

    /**
     * Get the registered instantiation of the instance
     * @param string $abstract
     * @return object
     * @throws \InvalidArgumentException|\ReflectionException
     */
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

    /**
     * Registering a new instance object into  
     * the container as a singleton instance 
     * @param string $abstract
     * @param callable|null|object|string $concrete
     * @return \Illuminate\Core\Container|self
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->register($abstract, $concrete, true);
        return $this;
    }

    /**
     * Registering a new instance
     * @param string $abstract
     * @param callable|null|object|string $concrete
     * @param bool $singleton
     * @return void
     */
    private function register($abstract, $concrete, $singleton)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!is_object($concrete) && is_callable($concrete)) {
            $concrete = call_user_func($concrete, $this);
        }

        $this->_instances[$abstract] = compact('concrete', 'singleton');
    }

    /**
     * Resolving the registered instance called
     * @param string|object $concrete
     * @return object
     * @throws \ReflectionException
     */
    private function resolve($concrete)
    {
        $class = new \ReflectionClass($concrete);
        $dependencies = $this->resolveDependencies($class->getConstructor());

        return $class->newInstanceArgs($dependencies);
    }

    /**
     * Resolving dependencies that the instance need
     * @param \ReflectionMethod $method
     * @param array $args
     * @return array
     * @throws \ReflectionException
     */
    private function resolveDependencies($method, array $args = [])
    {
        $resolvedDependencies = [];

        if (!$method instanceof \ReflectionMethod) {
            return $resolvedDependencies;
        }

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType() instanceof \ReflectionNamedType) {
                $instanceName = $parameter->getType()->getName();

                $resolvedDependencies[] = call_user_func_array(
                    [$this, array_key_exists($instanceName, $this->_instances) ? 'make' : 'resolve'],
                    [$instanceName]
                );

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
