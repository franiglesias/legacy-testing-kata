<?php

namespace Quotebot;

use Quotebot\Application\SendAllQuotes;
use Quotebot\Application\SendAllQuotesHandler;

class Application
{
    /** main application method */
    public static function main(array $args = null)
    {
        $bot = new AutomaticQuoteBot();
        $sendAllQuotes = new SendAllQuotes('FAST');

        $sendAllQuotesHandler = new SendAllQuotesHandler($bot);

        ($sendAllQuotesHandler)($sendAllQuotes);
    }
}
