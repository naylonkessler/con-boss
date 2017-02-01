<?php

namespace ConBoss\Support;

use ConBoss\Container;
use ConBoss\Support\Reflector;

/**
 * Resolver support class of ConBoss.
 * The resolver has the core of resolving target bindings by default method.
 */
class Resolver
{
    /**
     * An instance of reflector.
     * @var \ConBoss\Support\Reflector
     */
    protected $reflector = null;

    /**
     * Resolver constructor.
     * @param  \ConBoss\Support\Reflector  $reflector
     */
    public function __construct(Reflector $reflector = null)
    {
        if ( ! $reflector) {
            $reflector = new Reflector();
        }

        $this->reflector = $reflector;
    }

    /**
     * Resolve a received target.
     * @param  mixed  $target  Target to resolve
     * @param  \ConBoss\container  $container  Container instance
     * @return mixed
     */
    public function resolve($target, Container $container)
    {
        $instance = $target;

        if (is_string($target)) {
            $instance = $this->fromString($target, $container);
        }

        if (is_callable($target)) {
            $instance = $this->fromCallable($target, $container);
        }

        return $instance;
    }

    /**
     * Resolve a received target from callable.
     * @param  mixed  $target  Target to resolve
     * @param  \ConBoss\container  $container  Container instance
     * @return mixed
     */
    protected function fromCallable($target, Container $container)
    {
        return call_user_func($target, $container);
    }

    /**
     * Resolve a received target from string.
     * @param  mixed  $target  Target to resolve
     * @param  \ConBoss\container  $container  Container instance
     * @return mixed
     */
    protected function fromString($target, Container $container)
    {
        $info = $this->reflector->infoOf($target);
        $arguments = [];

        foreach ($info->dependencies as $dependency) {
            $arguments[] = $container->get($dependency);
        }

        return $this->reflector->newOf($target, $arguments);
    }
}