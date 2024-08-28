<?php

namespace App\Predictions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PredictionHandler
{
    /**
     * @param Array<string, PredictionUser> $users
     */
    function __construct(public array $users = [], public ?Prediction $prediction = NULL)
    {
    }

    function getUser(string $from): PredictionUser
    {
        return $this->users[$from] ??= new PredictionUser($from);
    }

    public static function load(): self
    {
        return Cache::get('pp', new self());
    }

    public function pushMessage(Message $msg): string
    {
        $usr = $this->getUser($msg->from);
        Log::info("User", ["user" => $usr]);

        if (!$msg->isValid()) {
            Log::info("Invalid message", ["msg" => $msg]);
            return "";
        }

        if ($msg->cmd == "c") {
            Log::info("Checking points", ["msg" => $msg]);
            return "@" . $msg->from . ": " . $usr->points;
        }

        if ($msg->cmd == "p") {
            if ($this->prediction != NULL) {
                Log::info("Prediction already exists", ["msg" => $msg]);
                return "@" . $msg->from . ": there is an active prediction";
            }

            $this->prediction = new Prediction($msg);
            Log::info("Prediction created", ["prediction" => $this->prediction]);
            return "";
        }

        if ($msg->cmd == "r") {
            Log::info("Resolving prediction", ["msg" => $msg]);
            if ($this->prediction === NULL) {
                Log::info("No active prediction", ["msg" => $msg]);
                return "@" . $msg->from . ": there is no active prediction";
            }

            $winner = intval(substr($msg->text, 3));
            Log::info("Resolving prediction", ["winner" => $winner]);
            if ($winner == 0) {
                Log::error("Winner is 0", ["winner" => $winner]);
                return "@" . $msg->from . ": invalid r syntax e.g.: !r 1";
            }

            Log::info("Resolving prediction", ["options" => count($this->prediction->options)]);
            if (count($this->prediction->options) < $winner) {
                Log::error("Winner to large", ["winner" => $winner, "options" => count($this->prediction->options)]);
                return "@" . $msg->from . ": FAILED RESOLVE, Winner to large";
            }

            foreach ($this->users as $user) {
                $user->resolve($this->prediction, $winner - 1);
            }
            $this->prediction = NULL;
            return "";
        }

        if ($this->prediction != NULL) {
            Log::info("Predicting", ["msg" => $msg]);
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
