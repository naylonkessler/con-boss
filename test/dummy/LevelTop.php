<?php

namespace ConBoss\Test\Mock;

class LevelTop
{
    public $second;

    public function __construct(LevelSecond $second)
    {
        $this->second = $second;
    }
}