<?php

namespace App\Console\Commands;

use App\PP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class IrcListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:irc-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /* $bot =  new \App\Irc\Bot('teej_dv', 'irc.chat.twitch.tv'); */
        /* $bot->on('connecting', function () { */
        /*     echo "Connecting...\n"; */
        /* })->on('connected', function () { */
        /*     echo "Connected!\n"; */
        /* })->on('chat', function ($e, $bot) { */
        /*     echo "CHAT($e->channel): $e->from: $e->text\n"; */
        /* })->on('welcome', function ($e, $bot) { */
        /*     //We list all the channels. This will trigger the 'list' event */
        /*     $bot->listChannels(); */
        /* }) */
        /**/
        /* ; */
        /**/
        /* $bot->connect(); */

        $server = 'irc.chat.twitch.tv';
        $port = 6667;
        $nickname = 'teej_dv';
        $token = env('TWITCH_OAUTH_TOKEN');

        // Connect to the IRC server
        $errNo = null;
        $errStr = null;
        $socket = @fsockopen($server, $port, $errNo, $errStr, 30);

        echo "errNo: $errNo\n";
        echo "errStr: $errStr\n";

        if (!$socket) {
            die("Unable to connect to IRC server\n");
        } else {
            echo "Connected to IRC\n";
        }

        $eof = feof($socket);
        if ($eof) {
            die("Connection closed immediately after opening.\n");
        }

        // Send authentication details
        fwrite($socket, "PASS $token\r\n");
        fwrite($socket, "NICK $nickname\r\n");

        // Join a channel
        $channel = '#ThePrimeagen';
        fwrite($socket, "JOIN $channel\r\n");
        echo "Joined $channel\n";

        $eof = feof($socket);
        if ($eof) {
            echo "OH NO";
            die("Connection closed immediately after opening.\n");
        }

        $pp = new PP();

        while (!feof($socket)) {
            $data = fgets($socket, 512);
            // Respond to PINGs to keep the connection alive
            if (strpos($data, 'PING') !== false) {
                fwrite($socket, "PONG :tmi.twitch.tv\r\n");
                continue;
            }

            $message = new IrcMessage($data);
            if ($message->username == NULL || $message->message == NULL) {
                continue;
            }

            /* if (strtolower($message->username) !== "theprimeagen") { */
            /*     continue; */
            /* } */
            /* echo "$message\n"; */

            try {
                $pushed = $pp->pushMessage($message->username, $message->message);
                echo "$pushed\n";
                if ($pushed !== "") {
                    fwrite($socket, "PRIVMSG $channel :$pushed\r\n");
                    continue;
                }

                if ($pp->prediction !== NULL) {
                    $predictions = $pp->prediction->options;
                    $predictions = array_map(function ($prediction) {
                        return [
                            'option' => $prediction->option,
                            'points' => $prediction->points,
                        ];
                    }, $predictions);
                    Cache::set("prediction_prompt", $pp->prediction->prediction);
                    Cache::set("prediction_options", $predictions);
                } else {
                    Cache::set("prediction_prompt", "");
                }
            } catch (\Throwable $e) {
            }

            /* $predictions = [ */
            /*     new \App\PPPredictionOption("Adam Almore", 250), */
            /*     new \App\PPPredictionOption("ThePrimeagen", 100), */
            /*     new \App\PPPredictionOption("teej_dv", 35), */
            /* ]; */


            /* Cache::set("prediction_prompt", "Who misses the first shot"); */
            /* Cache::set("prediction_options", $predictions); */

            /* // Example: Send a message to the channel */
            /* if (strpos($data, '!hello') !== false) { */
            /*     fwrite($socket, "PRIVMSG $channel :Hello, World!\r\n"); */
            /* } */
        }

        // Close the connection
        fclose($socket);
    }
}
