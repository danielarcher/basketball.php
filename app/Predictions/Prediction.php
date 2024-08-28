<?php

namespace App\Predictions;

class Prediction
{
    public bool $valid;
    public string $prediction;
    /** @var Array<Option> */
    public array $options;

    // !p <time> !ntehountehou !noehunoetuh
    function __construct(Message $msg)
    {
        $this->options = [];

        $options = explode("!", $msg->text);
        foreach (array_slice($options, 2) as $option) {
            array_push($this->options, new Option($option));
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
