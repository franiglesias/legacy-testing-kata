<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Infrastructure\QuoteProposalPublisher;
use Quotebot\Infrastructure\SystemTimeService;
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
        $proposalPublisher = new QuoteProposalPublisher();
        $timeService = new SystemTimeService();

        $blogAuctionTask = new BlogAuctionTask(
            $marketDataRetriever,
            $proposalPublisher,
            $timeService
        );

        self::$bot = self::$bot ?? new AutomaticQuoteBot($blogAuctionTask);
        self::$bot->sendAllQuotes('FAST');
    }
}
