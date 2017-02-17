<?php

namespace ConBoss\Test\Mock;

class NestedHasScalar
{
    public $hasScalar;

    public $another;

    public function __construct(HasScalar $hasScalar, Another $another)
    {
        $this->hasScalar = $hasScalar;
        $this->another = $another;
    }
}