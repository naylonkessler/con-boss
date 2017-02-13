<?php

namespace ConBoss\Support;

use ReflectionClass;
use ReflectionParameter;
use StdClass;

/**
 * Reflector support class of ConBoss.
 * The reflector acts like a wrapper for some reflection operations.
 */
class Reflector
{
    /**
     * Return info about some received target.
     *
     * @param  string  $target  A target to grab info
     * @return \StdClass
     */
    public function infoOf($target)
    {
        $info = new StdClass();

        $params = $this->getParameters($target);

        $info->dependencies = array_map([$this, 'infoOfOne'], $params);

        return $info;
    }

    /**
     * Return new instance of received target.
     *
     * @param  string  $target  A target to instantiate
     * @param  array  $args  Arguments for instance
     * @return mixed
     */
    public function newOf($target, array $args = [])
    {
        $class = new ReflectionClass($target);

        return $class->newInstanceArgs($args);
    }

    /**
     * Return the parameters of received target.
     *
     * @param  string  $target  A target to grab parameters
     * @return array
     */
    protected function getParameters($target)
    {
        $class = new ReflectionClass($target);

        if ( ! $class->hasMethod('__construct')) return [];

        return $class->getMethod('__construct')->getParameters();
    }

    /**
     * Return info about a single received parameter.
     *
     * @param  ReflectionParameter  $param  A parameter object to inspect
     * @return mixed
     */
    protected function infoOfOne(ReflectionParameter $param)
    {
        $class = $param->getClass();
        $hasDefault = $param->isDefaultValueAvailable();

        if ($class) return $class->name;

        if ($hasDefault) return $param->getDefaultValue();

        return '$'.$param->getName();
    }
}
