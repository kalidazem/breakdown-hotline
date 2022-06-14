<?php

namespace BreakdownHotline\DependencyInjection;

use InvalidArgumentException;

class Container implements \ArrayAccess
{

    private $container;
    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists(
            $offset,
            $this->container
        );
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists(
            $offset,
            $this->container
        )) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Container does not have have a value stored     for the "$s" key',
                    $offset
                )
            );
        }

        return $this->container[$offset] instanceof \Closure ?
            $this->container[$offset]($this) : $this->container[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset,  $value): void
    {
        $this->container[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    public function service($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

        return function (Container $container) use ($callable) {
            static $object;

            if ($object === null) {
                $object = $callable($container);
            }
            return $object;
        };
    }

    public function configure($serviceProviders)
    {

        if (!is_array($serviceProviders)) {
            $serviceProviders = array($serviceProviders);
        }

        foreach ($serviceProviders as $serviceProvider) {
            $this->register($serviceProvider);
        }
    }

    private function register($serviceProvider)
    {
        if (is_string($serviceProvider)) {
            $serviceProvider = new $serviceProvider();
        }

        if (!$serviceProvider instanceof ServiceProviderInterface) {
            throw new InvalidArgumentException('Service provider must implement the ServiceProviderInterface');
        }

        $serviceProvider->register($this);
    }
}
