<?php

namespace Quotebot;

class Application
{
    /** main application method */
    public static function main(array $args = null)
    {
        $bot = new AutomaticQuoteBot();
        $bot->sendAllQuotes('FAST');
    }
}
