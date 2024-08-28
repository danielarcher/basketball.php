<?php

namespace App\Predictions;

use Illuminate\Support\Facades\Log;

class PredictionUser
{
    public string $name = "";
    public int $points = 0;
    /** @var Array<string, UserPrediction> */
    public array $predictions = [];

    function __construct(string $name)
    {
        $this->name = $name;
        $this->points = 1000;
        $this->predictions = [];
    }

    public function __toString(): string
    {
        return "user: $this->name -- $this->points";
    }

    public function predict(Prediction $pred, int $point, int $option): void
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
        $this->predictions[$pred->prediction] = new UserPrediction($pointsBet, $option);
        $pred->options[$option]->points += $pointsBet;
    }

    public function resolve(Prediction $pred, int $winningOption): void
    {
        $winningTotalPoints = $pred->totalPoints();
        if ($winningTotalPoints == 0) {
            return;
        }

        Log::info("Resolving prediction", ["user" => $this->name]);
        foreach ($this->predictions as $k => $v) {
            Log::info("prediction", ["prediction" => $k, "points" => $v->points, "option" => $v->option]);
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
