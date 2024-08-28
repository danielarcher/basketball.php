<?php

namespace App\Livewire;

use App\Predictions\Message;
use App\Predictions\PredictionHandler;
use App\Predictions\Scores;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Adam extends Component
{
    public function render()
    {
        $options = Cache::get('prediction_options', []);
        $totalPoints = 1;
        foreach ($options as $option) {
            $totalPoints += $option["points"];
        }

        foreach ($options as $option) {
            $option["percentage"] = $option["points"] / $totalPoints * 100;
        }

        return view('livewire.adam', [
            'terminalScore' => Scores::terminalScore(),
            'laraconScore' => Scores::laraconScore(),
            'message_count' => Cache::get('message_count'),
            'prompt' => Cache::get('prediction_prompt'),
            'options' => $options,
            'totalPoints' => $totalPoints,
        ]);
    }

    public function fakePrediction()
    {
        $pp = PredictionHandler::load();
        $pp->pushMessage(new Message('teej_dv', '!p testing !first !second !third'));
        $pp->save();
    }

    public function fakePlay()
    {
        logger()->info("Fake play");
        $pp = PredictionHandler::load();
        $option = mt_rand(1,3);
        $amount = mt_rand(1, 300);
        $pp->pushMessage(new Message(mt_rand(), "!{$option} $amount"));
        $pp->save();
    }

    public function fakeResolve()
    {
        $pp = PredictionHandler::load();
        $pp->pushMessage(new Message('teej_dv', '!r 1'));
        $pp->save();
    }
}
