<?php

namespace Quotebot;


class Application {
    public static function main(array $args = null)
    {
        $bot = new AutomaticQuoteBot();
        $bot->sendAllQuotes('FAST');
    }
}
