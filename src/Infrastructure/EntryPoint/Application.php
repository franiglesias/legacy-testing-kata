<?php

namespace Quotebot\Infrastructure\EntryPoint;

use Quotebot\AutomaticQuoteBot;

class Application
{
    /** main application method */
    public static function main(array $args = null)
    {
        $args = $args ?? ['APP_ENV' => 'prod'];

        if ($args['APP_ENV'] === 'prod') {
            $bot = new AutomaticQuoteBot();
            $bot->sendAllQuotes('FAST');
        }
    }
}
