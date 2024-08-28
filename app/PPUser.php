<?php

namespace App;

class PPUser
{
    public string $name = "";
    public int $points = 0;
    /** @var Array<string, PPUserPrediction> */
    public array $predictions = [];

    function __construct(string $name)
    {
        $this->name = $name;
        $this->points = 1000;
        $this->predictions = [];
    }

    public function __toString()
    {
        return "user: $this->name -- $this->points";
    }

    public function predict(PPPrediction $pred, int $point, int $option)
    {
        if (isset($this->predictions[$pred->prediction]) && $prev = $this->predictions[$pred->prediction]) {
            if ($prev->resolved) {
                return;
            }
            $this->points += $prev->points;
            $pred->options[$prev->option]->points -= $prev->points;
        }

        $pointsBet = min($point, $this->points);
        if ($pointsBet == 0) {
            return;
        }

        if (count($pred->options) <= $option || $option < 0) {
            return;
        }

        $this->points -= $pointsBet;
        $this->predictions[$pred->prediction] = new PPUserPrediction($pointsBet, $option);
        $pred->options[$option]->points += $pointsBet;
    }

    public function resolve(PPPrediction $pred, int $winningOption)
    {
        $winningTotalPoints = $pred->totalPoints();
        if ($winningTotalPoints == 0) {
            return;
        }

        echo "for $this->name\n";
        foreach ($this->predictions as $k => $v) {
            echo "prediction: $k => $v\n";
        }

        if (isset($this->predictions[$pred->prediction]) && $our = $this->predictions[$pred->prediction]) {
            if ($our->resolved) {
                return;
            }
            if ($our->option == $winningOption) {
                $this->points += floor(
                    ($our->points /
                        $pred->options[$winningOption]->points) *
                    $winningTotalPoints
                );
            }
            $our->resolved = true;
        }
    }
}
