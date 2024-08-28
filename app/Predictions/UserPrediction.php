<?php

namespace App\Predictions;

class UserPrediction
{
    function __construct(public int $points, public int $option, public bool $resolved = false)
    {
    }

    function __toString(): string
    {
        return "PPUserPrediction($this->option): $this->points";
    }
}
