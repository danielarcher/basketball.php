<?php

namespace App\Predictions;

class Prediction
{
    /**
     * @param Array<Option> $options
     */
    function __construct(Message $msg, public array $options = [], public string $prediction = "")
    {
        $options = explode("!", $msg->text);
        foreach (array_slice($options, 2) as $option) {
            $this->options[] = new Option($option);
        }

        $this->prediction = substr($options[1], 2);
    }

    public function totalPoints(): int
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
