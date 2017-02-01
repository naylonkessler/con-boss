<?php

namespace ConBoss;

use StdClass;

use ConBoss\Support\Resolver;

/**
 * Container class of ConBoss.
 * The container is the main class of dependency manager and controls all
 * binding operations.
 */
class Container
{
    /**
     * Collection of registered bindings.
     * @var array
     */
    protected $bindings = [];

    /**
     * An instance of resolver.
     * @var \ConBoss\Support\Resolver
     */
    protected $resolver = null;

    /**
     * Collection of shared instances.
     * @var array
     */
    protected $shared = [];

    /**
     * Container constructor.
     * @param  \ConBoss\Support\Resolver  $resolver
     */
    public function __construct(Resolver $resolver = null)
    {
        if ( ! $resolver) {
            $resolver = new Resolver();
        }

        $this->resolver = $resolver;
    }

    /**
     * Register a bind on container.
     * @param  string  $source  Source name of binding
     * @param  mixed  $target  Target to resolve the binding
     * @param  boolean  $shared  If binding is shared
     * @return \ConBoss\Container
     */
    public function bind($source, $target, $shared = false)
    {
        $binding = new StdClass();
        $binding->shared = $shared;
        $binding->target = $target;

        $this->bindings[$source] = $binding;

        return $this;
    }

    /**
     * Get the result of a resolved binding from container.
     * @param  mixed  $source  Source name for get
     * @return mixed
     */
    public function get($source)
    {
        if ($source === null) return null;

        $source = (array) $source;

        $instances = array_map([$this, 'getOne'], $source);

        return count($instances) > 1? $instances : $instances[0];
    }

    /**
     * Check if container has a binding.
     * @param  string  $source  Source name for check
     * @return boolean
     */
    public function has($source)
    {
        return array_key_exists($source, $this->bindings);
    }

    /**
     * Register a shared bind on container.
     * @param  string  $source  Source name of binding
     * @param  mixed  $target  Target to resolve the binding
     * @return \ConBoss\Container
     */
    public function share($source, $target)
    {
        return $this->bind($source, $target, true);
    }

    /**
     * Return a binding info for a received source.
     * @param  mixed  $source  Source name of binding
     * @return \StdClass
     */
    protected function bindingFor($source)
    {
        if ($this->has($source)) return $this->bindings[$source];

        $binding = new StdClass();
        $binding->shared = false;
        $binding->target = $source;

        return $binding;
    }

    /**
     * Get the result of a resolved binding for one source.
     * @param  mixed  $one  Source name for get
     * @return mixed
     */
    protected function getOne($one)
    {
        $binding = $this->bindingFor($one);

        $returnShared = $binding->shared && $this->hasShared($one);

        if ($returnShared) return $this->shared[$one];

        $instance = $this->resolver->resolve($binding->target, $this);

        if ($binding->shared) {
            $this->shared[$one] = $instance;
        }

        return $instance;
    }

    /**
     * Check if container has a shared instance.
     * @param  string  $source  Source name for check
     * @return boolean
     */
    protected function hasShared($source)
    {
        return array_key_exists($source, $this->shared);
    }
}