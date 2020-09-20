<?php

namespace Quotebot;

use Dotenv\Dotenv;
use MarketStudyVendor;
use Quotebot\Infrastructure\BlogAdSpaceProvider;
use Quotebot\Infrastructure\LocalQuoteProposalPublisher;
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
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR. '../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__.DIRECTORY_SEPARATOR.'..');
            $dotenv->load();
        }

        $environment = $_ENV['APP_ENV'] ?? 'PROD';

        if ($environment === 'LOCAL') {
            $proposalPublisher = new LocalQuoteProposalPublisher();
        } else {
            $proposalPublisher = new QuoteProposalPublisher();
        }

        $marketDataRetriever = new VendorDataRetriever(new MarketStudyVendor());
        $timeService = new SystemTimeService();

        $blogAuctionTask = new BlogAuctionTask(
            $marketDataRetriever,
            $proposalPublisher,
            $timeService
        );

        $adSpaceProvider = new BlogAdSpaceProvider();

        self::$bot = self::$bot ?? new AutomaticQuoteBot(
                $blogAuctionTask,
                $adSpaceProvider
            );
        self::$bot->sendAllQuotes('FAST');
    }
}
