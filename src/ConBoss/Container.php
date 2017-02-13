<?php

namespace ConBoss;

use StdClass;

use ConBoss\Support\Reflector;

/**
 * Container class of ConBoss.
 * The container is the main class of dependency manager and controls all
 * binding operations.
 */
class Container
{
    /**
     * Collection of registered bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * An instance of reflector.
     *
     * @var \ConBoss\Support\Reflector
     */
    protected $reflector = null;

    /**
     * Collection of shared instances.
     *
     * @var array
     */
    protected $shared = [];

    /**
     * Container constructor.
     *
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
     * Register a bind on container.
     *
     * @param  string  $source  Source name of binding
     * @param  mixed  $target  Target to resolve the binding
     * @param  boolean  $shared  If binding is shared
     * @return \ConBoss\Container
     */
    public function bind($source, $target, $shared = false)
    {
        $binding = new StdClass();
        $binding->name = $source;
        $binding->shared = $shared;
        $binding->target = $target;

        $this->bindings[$source] = $binding;

        return $this;
    }

    /**
     * Get the result of a resolved binding from container.
     *
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
     *
     * @param  string  $source  Source name for check
     * @return boolean
     */
    public function has($source)
    {
        return array_key_exists($source, $this->bindings);
    }

    /**
     * Register a shared bind on container.
     *
     * @param  string  $source  Source name of binding
     * @param  mixed  $target  Target to resolve the binding
     * @return \ConBoss\Container
     */
    public function share($source, $target)
    {
        return $this->bind($source, $target, true);
    }

    /**
     * Unregister a bind on container.
     *
     * @param  string  $source  Source name of binding
     * @return \ConBoss\Container
     */
    public function unbind($source)
    {
        if ($this->has($source)) {
            unset($this->bindings[$source]);
        }

        return $this;
    }

    /**
     * Return a binding info for a received source.
     *
     * @param  mixed  $source  Source name of binding
     * @return \StdClass
     */
    protected function bindingFor($source)
    {
        if ($this->has($source)) return $this->bindings[$source];

        $binding = new StdClass();
        $binding->name = $source;
        $binding->shared = false;
        $binding->target = $source;

        return $binding;
    }

    /**
     * Resolve a received target from callable.
     *
     * @param  mixed  $target  Target to resolve
     * @return mixed
     */
    protected function fromCallable($target)
    {
        return call_user_func($target, $this);
    }

    /**
     * Resolve a received target from string.
     *
     * @param  mixed  $target  Target to resolve
     * @return mixed
     */
    protected function fromString($target)
    {
        $info = $this->reflector->infoOf($target);

        $arguments = array_map([$this, 'oneFromString'], $info->dependencies);

        return $this->reflector->newOf($target, $arguments);
    }

    /**
     * Resolve a received binding for a variable.
     *
     * @param  \StdClass  $variable  Binding to resolve
     * @return mixed
     */
    protected function fromVariable(StdClass $binding)
    {
        if ( ! $this->has($binding->name)) return null;

        return $binding->target;
    }

    /**
     * Get the result of a resolved binding for one source.
     *
     * @param  mixed  $one  Source name for get
     * @return mixed
     */
    protected function getOne($one)
    {
        $binding = $this->bindingFor($one);

        $returnShared = $binding->shared && $this->hasShared($one);

        if ($returnShared) return $this->shared[$one];

        $instance = $this->resolve($binding);

        if ($binding->shared) {
            $this->shared[$one] = $instance;
        }

        return $instance;
    }

    /**
     * Check if container has a shared instance.
     *
     * @param  string  $source  Source name for check
     * @return boolean
     */
    protected function hasShared($source)
    {
        return array_key_exists($source, $this->shared);
    }

    /**
     * Check if reveived target if a variable.
     *
     * @param  string  $target  Target to check
     * @return boolean
     */
    protected function isTargetVariable($target)
    {
        return is_string($target) && $target[0] === '$';
    }

    /**
     * Mapper function for resolution from string.
     *
     * @param  mixed  $dependency  Some dependency to get
     * @return mixed
     */
    protected function oneFromString($dependency)
    {
        return $this->get($dependency);
    }

    /**
     * Resolve a received binding.
     *
     * @param  StdClass  $binding  Binding to resolve
     * @return mixed
     */
    protected function resolve(StdClass $binding)
    {
        $instance = $binding->target;

        if ($this->isTargetVariable($binding->name)) {
            return $this->fromVariable($binding);
        }

        if (is_string($binding->target)) {
            return $this->fromString($binding->target);
        }

        if (is_callable($binding->target)) {
            return $this->fromCallable($binding->target);
        }

        return $instance;
    }
}
