<?php

namespace App\Console\Commands;

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
        if ($this->raw[0] === ':') {
            // Extract the prefix (everything before the first space)
            $spacePos = strpos($this->raw, ' ');
            $this->prefix = substr($this->raw, 1, $spacePos - 1);
            $this->raw = substr($this->raw, $spacePos + 1);
        }

        // Extract the command (e.g., PRIVMSG)
        $spacePos = strpos($this->raw, ' ');
        $this->command = substr($this->raw, 0, $spacePos);
        $this->raw = substr($this->raw, $spacePos + 1);

        // Extract the parameters (e.g., channel and message)
        $params = explode(' :', $this->raw, 2);

        // First part is usually the channel name
        $this->params = explode(' ', $params[0]);
        $this->channel = $this->params[0];

        // If there is a message after the colon
        if (isset($params[1])) {
            $this->message = $params[1];
        }

        // Extract the username from the prefix
        if ($this->prefix) {
            $exclamationPos = strpos($this->prefix, '!');
            if ($exclamationPos !== false) {
                $this->username = substr($this->prefix, 0, $exclamationPos);
            }
        }
    }

    public function __toString(): string
    {
        // Format the message for pretty printing
        $output = "IRC Message:\n";
        $output .= "-------------------------\n";
        $output .= sprintf("Username: %s\n", $this->username ?? 'N/A');
        $output .= sprintf("Message: %s\n", $this->message ?? 'N/A');
        $output .= "-------------------------\n";

        return $output;
    }
}
