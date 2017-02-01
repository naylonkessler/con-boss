<?php

namespace ConBoss\Test\Mock;

class LevelSecond
{
    public $third;

    public function __construct(LevelThird $third)
    {
        $this->third = $third;
    }
}