<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Infrastructure\VendorDataRetriever;

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
        $marketDataRetriever = new VendorDataRetriever(new MarketStudyVendor());
        $blogAuctionTask = new BlogAuctionTask($marketDataRetriever);

        self::$bot = self::$bot ?? new AutomaticQuoteBot($blogAuctionTask);
        self::$bot->sendAllQuotes('FAST');
    }
}
