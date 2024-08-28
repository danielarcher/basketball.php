<?php

namespace App;

class PP
{
    /** @var Array<string, PPUser> */
    public array $users;

    public ?PPPrediction $prediction = NULL;

    function __construct()
    {
        $this->users = [];
        $this->prediction = NULL;
    }

    function getUser(string $from)
    {
        if (!isset($this->users[$from])) {
            $this->users[$from] = new PPUser($from);
        }

        return $this->users[$from];
    }

    public function pushMessage(string $from, string $text)
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
}
