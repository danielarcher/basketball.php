<?php

namespace Tests\Feature;

use App\Console\Commands\IrcMessage;
use PHPUnit\Framework\TestCase;

class IrcMessageTest extends TestCase
{
    public function testParsingFullMessage()
    {
        $rawMessage = ':nickname!username@host PRIVMSG #channel :Hello, world!';
        $ircMessage = new IrcMessage($rawMessage);

        $this->assertEquals('nickname', $ircMessage->username);
        $this->assertEquals('PRIVMSG', $ircMessage->command);
        $this->assertEquals('#channel', $ircMessage->channel);
        $this->assertEquals('Hello, world!', $ircMessage->message);
        $this->assertEquals(['#channel'], $ircMessage->params);
    }

    public function testParsingJoinMessage()
    {
        $rawMessage = ':nickname!username@host JOIN #channel';
        $ircMessage = new IrcMessage($rawMessage);

        $this->assertEquals('nickname', $ircMessage->username);
        $this->assertEquals('JOIN', $ircMessage->command);
        $this->assertEquals('#channel', $ircMessage->channel);
        $this->assertNull($ircMessage->message);
    }

    public function testParsingPartMessage()
    {
        $rawMessage = ':nickname!username@host PART #channel :Goodbye!';
        $ircMessage = new IrcMessage($rawMessage);

        $this->assertEquals('nickname', $ircMessage->username);
        $this->assertEquals('PART', $ircMessage->command);
        $this->assertEquals('#channel', $ircMessage->channel);
        $this->assertEquals('Goodbye!', $ircMessage->message);
    }

    public function testParsingNoticeMessage()
    {
        $rawMessage = ':server NOTICE * :Server message';
        $ircMessage = new IrcMessage($rawMessage);

        $this->assertEquals('server', $ircMessage->prefix);
        $this->assertNull($ircMessage->username);
        $this->assertEquals('NOTICE', $ircMessage->command);
        $this->assertEquals('*', $ircMessage->channel);
        $this->assertEquals('Server message', $ircMessage->message);
    }

    public function testToString()
    {
        $rawMessage = ':nickname!username@host PRIVMSG #channel :Hello, world!';
        $ircMessage = new IrcMessage($rawMessage);

        $expectedOutput = "IRC Message:\n-------------------------\nUsername: nickname\nMessage: Hello, world!\n-------------------------\n";
        $this->assertEquals($expectedOutput, (string)$ircMessage);
    }
}
