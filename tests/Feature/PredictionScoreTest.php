<?php

namespace Tests\Feature;

use App\Predictions\PredictionHandler;
use Tests\TestCase;

class PredictionScoreTest extends TestCase
{
    public function test_prediction_points_new_prediction()
    {
        $pp = new PredictionHandler();
        $out = $pp->pushMessage("theprimeagen", "!p Will teej make his first basket? !one option !two options !three twitch sucks");
        $this->assertEmpty($out, "failed:  output from adding mod prediction: $out");
        $this->assertNotNull($pp->prediction, "failed: prediction was not created");
    }

    public function test_prediction_points_full_calculation()
    {
        $pp = new PredictionHandler();
        $pp->pushMessage("theprimeagen", "!p Will teej make his first basket? !one option !two options !three twitch sucks");
        $pp->pushMessage("teej", "!1 69");
        $this->assertNotNull($pp->prediction, "failed: prediction was not created");
        $this->assertEquals(931, $pp->getUser("teej")->points, "failed: teej should have 931 points");
        $this->assertEquals(69, $pp->prediction->options[0]->points);
        $this->assertEquals(69, $pp->prediction->totalPoints());

        $pp->pushMessage("foobar", "!1 42");
        $this->assertEquals(111, $pp->prediction->options[0]->points);
        $this->assertEquals(958, $pp->getUser("foobar")->points);

        $pp->pushMessage("bash", "!2 89");
        $this->assertEquals(89, $pp->prediction->options[1]->points);
        $this->assertEquals(200, $pp->prediction->totalPoints());

        /**
         * resolve prediction
         */
        $out = $pp->pushMessage("theprimeagen", "!r 1");
        $this->assertEmpty($out);
        $this->assertNull($pp->prediction, "failed: prediction should be null");
    }
}
