<?php

namespace App;

class PPPrediction
{
    public bool $valid;
    public string $prediction;
    /** @var Array<PPPredictionOption> */
    public array $options;

    // !p <time> !ntehountehou !noehunoetuh
    function __construct(PPMessage $msg)
    {
        $this->options = [];

        $options = explode("!", $msg->text);
        foreach (array_slice($options, 2) as $option) {
            array_push($this->options, new PPPredictionOption($option));
        }

        $this->valid = true;
        $this->prediction = substr($options[1], 2);
    }

    public function totalPoints()
    {
        $total = 0;
        foreach ($this->options as $option) {
            $total += $option->points;
        }
        return $total;
    }

    public function __toString()
    {
        $out = "PPPrediction: $this->prediction\n";
        foreach ($this->options as $option) {
            $out .= "$option\n";
        }
        return $out;
    }
}
