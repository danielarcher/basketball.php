<?php

namespace App;

class PPMessage
{
    public string $text;
    public string $from;
    public bool $super;
    public string $cmd;
    public int $pointsPredicted;
    public int $predictedIndex;


    public $mods = [
        "nightshadedude" => true,
        "beastco" => true,
        "samhuckabee" => true,
        "theprimeagen" => true,
        "teej_dv" => true,
    ];

    function __construct(string $from, string $text)
    {
        $text = trim($text);

        $this->from = $from;
        $this->text = trim($text);

        $lower = strtolower($from);
        $this->super = isset($this->mods[$lower]) && $this->mods[$lower];

        if (strlen($text) < 2) {
            $this->cmd = "NONE";
        } else {
            $this->cmd = explode(" ", substr($text, 1), 2)[0];
        }

        $this->pointsPredicted = 0;
        switch ($this->cmd) {
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
                $items = explode(" ", $text);
                if (count($items) !== 2) {
                    $this->pointsPredicted = 0;
                    return;
                }
                $this->pointsPredicted = intval($items[1]);
        }

        $this->predictedIndex = intval($this->cmd) - 1;
    }

    function __toString()
    {
        return "PPMessage($this->from, $this->text, $this->cmd, " . strval($this->super) . ")";
    }

    function isValid()
    {
        if (!str_starts_with($this->text, "!")) {
            return false;
        }

        $isModMessage = $this->cmd == "p" || $this->cmd == "r";
        if ($isModMessage && $this->super) {
            return true;
        } else if ($isModMessage) {
            return false;
        }

        switch ($this->cmd) {
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
                return $this->pointsPredicted > 0;
            case "c":
                return $this->text === "!c";
            case "r":
                if (strlen($this->text) === 4) {
                    $parts = explode(" ", $this->text);
                    if (count($parts) == 2) {
                        return intval($parts[1]) > 0;
                    }
                }
        }
        return false;
    }
}
