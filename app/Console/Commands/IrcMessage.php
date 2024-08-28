<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;

class IrcMessage
{
    public function __construct(
        public string  $raw,
        public ?string $prefix = null,
        public ?string $username = null,
        public ?string $command = null,
        public array   $params = [],
        public ?string $channel = null,
        public ?string $message = null
    )
    {
        $this->parseMessage();
    }

    private function parseMessage(): void
    {
        // Check if the message starts with a colon (':'), which usually indicates the presence of a prefix
        if (Str::startsWith($this->raw, ':')) {
            [$this->prefix, $this->raw] = Str::of($this->raw)->substr(1)->explode(' ', 2);
        }

        // Extract the command (e.g., PRIVMSG)
        $spacePos = Str::position($this->raw, ' ');
        if ($spacePos !== false) {
            $this->command = Str::substr($this->raw, 0, $spacePos);
            $this->raw = Str::substr($this->raw, $spacePos + 1);
        } else {
            $this->command = $this->raw;
            $this->raw = '';
        }

        $params = Str::of($this->raw)->explode(' :', 2);

        // First part is usually the channel name
        $this->params = $params->first() ? explode(' ', $params->first()) : [];
        $this->channel = $this->params[0] ?? null;

        // If there is a message after the colon
        $this->message = $params->count() > 1 ? $params->last() : null;

        // Extract the username from the prefix
        if ($this->prefix) {
            $exclamationPos = Str::position($this->prefix, '!');
            if ($exclamationPos !== false) {
                $this->username = Str::substr($this->prefix, 0, $exclamationPos);
            }
        }
    }

    public function __toString(): string
    {
        return collect([
            'IRC Message:',
            '-------------------------',
            sprintf("Username: %s", $this->username ?? 'N/A'),
            sprintf("Message: %s", $this->message ?? 'N/A'),
            '-------------------------',
            '',
        ])->implode("\n");
    }
}
