<?php

namespace App\Predictions;

class UserPrediction
{
    public int $points = 0;
    public int $option = 0;
    public bool $resolved = false;

    function __construct($points, $option)
    {
        $this->points = $points;
        $this->option = $option;
        $this->resolved = false;
    }

    function __toString()
    {
        return "PPUserPrediction($this->option): $this->points";
    }
}
