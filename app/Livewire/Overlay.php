<?php

namespace App\Livewire;

use App\Predictions\Scores;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Overlay extends Component
{
    public function render()
    {
        /* dd(Cache::get('prediction_options')); */
        /* $options = Cache::get('prediction_options')->map(function ($option) { */
        /*     return $option->option; */
        /* }); */
        /* dd($options); */

        $options = Cache::get('prediction_options');
        $totalPoints = 0;
        foreach ($options as $option) {
            $totalPoints += $option["points"];
        }

        foreach ($options as $option) {
            $option["percentage"] = $option["points"] / $totalPoints * 100;
        }

        return view('livewire.overlay', [
            'terminalScore' => Scores::terminalScore(),
            'laraconScore' => Scores::laraconScore(),
            'message_count' => Cache::get('message_count'),
            'prompt' => Cache::get('prediction_prompt'),
            'options' => $options,
            'totalPoints' => $totalPoints,
        ]);
    }
}
