<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PP
{
    /**
     * @param Array<string, PPUser> $users
     * @param PPPrediction|null $prediction
     */
    function __construct(public array $users = [], public ?PPPrediction $prediction = NULL)
    {
    }

    function getUser(string $from): PPUser
    {
        return $this->users[$from] ??= new PPUser($from);
    }

    public static function load(): self
    {
        return Cache::get('pp', new self());
    }

    public function pushMessage(string $from, string $text): string
    {
        $msg = new PPMessage($from, $text);
        $usr = $this->getUser($from);
        echo "message: $msg\n";
        echo "user: $usr\n";

        if (!$msg->isValid()) {
            echo "NOT VALID";
            return "";
        }

        if ($msg->cmd == "c") {
            return "@" . $msg->from . ": " . $usr->points;
        }

        if ($msg->cmd == "p") {
            if ($this->prediction != NULL) {
                return "@" . $msg->from . ": there is an active prediction";
            }

            $pred = new PPPrediction($msg);
            if (!$pred->valid) {
                return "@" . $msg->from . ": Invalid prediction syntax";
            }

            $this->prediction = $pred;
            return "";
        }

        if ($msg->cmd == "r") {
            echo "HAYAYAYAYAYA";
            if ($this->prediction === NULL) {
                return "@" . $msg->from . ": there is no active prediction";
            }

            $winner = substr($msg->text, 3);
            echo "winner: \"$winner\"\n";
            $winner = intval($winner);
            if ($winner == 0) {
                return "@" . $msg->from . ": invalid r syntax e.g.: !r 1";
            }

            echo "predictions: " . count($this->prediction->options) . "\n";
            if (count($this->prediction->options) < $winner) {
                return "@" . $msg->from . ": FALIDE RESOLVE, Winner to large";
            }

            foreach ($this->users as $user) {
                $user->resolve($this->prediction, $winner - 1);
            }
            $this->prediction = NULL;
            return "";
        }

        if ($this->prediction != NULL) {
            $usr->predict($this->prediction, $msg->pointsPredicted, $msg->predictedIndex);
        }
        return "";
    }

    public function save(): void
    {
        Log::info("Saving PP");
        if ($this->prediction !== NULL) {
            $predictions = $this->prediction->options;
            $predictions = array_map(function ($prediction) {
                return [
                    'option' => $prediction->option,
                    'points' => $prediction->points,
                ];
            }, $predictions);
            Cache::put("prediction_prompt", $this->prediction->prediction);
            Cache::put("prediction_options", $predictions);
        } else {
            Cache::put("prediction_prompt", "");
        }
        Log::info("Saved PP", ["users" => $this->users, "prediction" => $this->prediction]);
        Cache::put('pp', $this);
    }
}
