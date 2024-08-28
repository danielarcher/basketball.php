<?php

namespace App;

class PPPredictionOption
{
    public string $option;
    public int $points;

    function __construct(string $option, int $points = 0)
    {
        $this->option = $option;
        $this->points = $points;
    }

    function __toString()
    {
        return "$this->option: $this->points";
    }
}
