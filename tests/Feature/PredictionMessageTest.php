<?php

namespace Tests\Feature;

use App\PP;
use App\PPMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PredictionMessageTest extends TestCase
{
    #[DataProvider("fakePlayProvider")]
    public function test_push_message_returns(string $cmd, string $text, bool $expected): void
    {
        $msg = new PPMessage($text, $cmd);
        $this->assertEquals($expected, $msg->isValid(), "Failed on ppmessage: $cmd | $text | $expected");
    }

    public static function fakePlayProvider(): array
    {
        return [
            ["!c", "foobar", true],
            ["!c", "nightshadedude", true],
            ["!p oneuh oeu", "nightshadedude", true],
            ["!p oneuh oeu", "foobar", false],
            ["!1 oneuh", "foobar", false],
            ["!1 1000", "foobar", true],
            ["!1 1000", "nightshadedude", true],
            ["!r 7", "nightshadedude", true],
            ["!r 7", "foobar", false],
        ];
    }

    public function test_push_message_returns_output(): void
    {
        $pp = new PP();
        $out = $pp->pushMessage("theprimeagen", "!p Will teej make his first basket? !one option !two options !three twitch sucks");
        $this->assertEmpty($out, "Output from adding mod prediction: $out");
    }
}
