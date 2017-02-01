<?php

namespace ConBoss\Test\Mock;

class Main
{
    public $some;

    public $another;

    public function __construct(Some $some, Another $another)
    {
        $this->some = $some;
        $this->another = $another;
    }
}