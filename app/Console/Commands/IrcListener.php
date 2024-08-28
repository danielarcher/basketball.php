<?php

namespace App\Console\Commands;

use App\Predictions\Message;
use App\Predictions\PredictionHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class IrcListener extends Command
{
    protected $signature = 'app:irc-listener';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server = 'irc.chat.twitch.tv';
        $port = 6667;
        $nickname = 'teej_dv';
        $token = env('TWITCH_OAUTH_TOKEN');

        $errNo = null;
        $errStr = null;
        $socket = @fsockopen($server, $port, $errNo, $errStr, 30);

        if (!$socket) {
            die("Unable to connect to IRC server\n");
        }
        Log::info("Connected to IRC");

        $eof = feof($socket);
        if ($eof) {
            Log::error("Connection closed immediately after opening");
            die("Connection closed immediately after opening.\n");
        }

        // Send authentication details
        fwrite($socket, "PASS $token\r\n");
        fwrite($socket, "NICK $nickname\r\n");

        // Join a channel
        $channel = '#ThePrimeagen';
        fwrite($socket, "JOIN $channel\r\n");
        Log::info("Joined channel", ["channel" => $channel]);

        $eof = feof($socket);
        if ($eof) {
            Log::error("Connection closed immediately after opening");
            die("Connection closed immediately after opening.\n");
        }

        while (!feof($socket)) {
            $data = fgets($socket, 512);
            // Respond to PINGs to keep the connection alive
            if (str_contains($data, 'PING')) {
                fwrite($socket, "PONG :tmi.twitch.tv\r\n");
                continue;
            }

            $message = new IrcMessage($data);
            if ($message->username == NULL || $message->message == NULL) {
                continue;
            }

            try {
                $pp = PredictionHandler::load();
                $pushed = $pp->pushMessage(Message::fromIrcMessage($message));
                if ($pushed !== "") {
                    fwrite($socket, "PRIVMSG $channel :$pushed\r\n");
                    continue;
                }
                $pp->save();
            } catch (\Throwable $e) {
                Log::error("Error processing message", ["message" => $message, "error" => $e->getMessage()]);
            }
        }

        Log::info("Closing connection");
        fclose($socket);
    }
}
