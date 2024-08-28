<?php

namespace App\Predictions;

class Option
{
    function __construct(public string $option, public int $points = 0) {}

    function __toString(): string
    {
        return "$this->option: $this->points";
    }
}
