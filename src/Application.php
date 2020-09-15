<?php

namespace Quotebot;

class Application
{
    private static $bot;

    public static function inject($bot)
    {
        self::$bot = $bot;
    }

    /** main application method */
    public static function main(array $args = null)
    {
        self::$bot = self::$bot ?? new AutomaticQuoteBot();
        self::$bot->sendAllQuotes('FAST');
    }
}
