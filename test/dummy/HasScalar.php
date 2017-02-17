<?php

namespace ConBoss\Test\Mock;

class HasScalar
{
    public $some;

    public $scalar;

    public $defaults;

    public function __construct(Some $some, $scalar, $defaults = 10)
    {
        $this->some = $some;
        $this->scalar = $scalar;
        $this->defaults = $defaults;
    }
}