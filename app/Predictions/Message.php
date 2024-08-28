<?php

namespace App\Predictions;

class Message
{
    public string $text;
    public string $from;
    public bool $super;
    public string $cmd;
    public int $pointsPredicted;
    public int $predictedIndex;

    function __construct(string $from, string $text)
    {
        $this->from = $from;
        $this->text = trim($text);
        $this->super = $this->isSuper();
        $this->cmd = preg_match('/!(\w+)/', $text, $matches) ? $matches[1] : "NONE";
        $this->pointsPredicted = match ($this->cmd) {
            "1", "2", "3", "4", "5" => intval(explode(" ", $text)[1]),
            default => 0,
        };
        $this->predictedIndex = intval($this->cmd) - 1;
    }

    public function isSuper(): bool
    {
        return in_array(strtolower($this->from), config('predictions.mods', []));
    }

    function __toString(): string
    {
        return "PPMessage($this->from, $this->text, $this->cmd, " . $this->super . ")";
    }

    function isValid(): bool
    {
        if (!str_starts_with($this->text, "!")) {
            return false;
        }

        $isModMessage = $this->cmd == "p" || $this->cmd == "r";
        if ($isModMessage) {
            return $this->super;
        }

        return match ($this->cmd) {
            "1", "2", "3", "4", "5" => $this->pointsPredicted > 0,
            "c" => $this->text === "!c",
            "r" => strlen($this->text) === 4 && intval(explode(" ", $this->text)[1]) > 0,
            default => false,
        };
    }
}
