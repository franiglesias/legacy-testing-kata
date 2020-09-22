<?php

namespace Quotebot\Infrastructure\EntryPoint;

use Dotenv\Dotenv;
use MarketStudyVendor;
use Quotebot\Domain\AutomaticQuoteBot;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Infrastructure\AdSpaceProvider\BlogAdSpaceProvider;
use Quotebot\Infrastructure\AdSpaceProvider\LocalAdSpaceProvider;
use Quotebot\Infrastructure\MarketDataRetriever\LocalMarketDataRetriever;
use Quotebot\Infrastructure\MarketDataRetriever\VendorDataRetriever;
use Quotebot\Infrastructure\ProposalPublisher\LocalQuoteProposalPublisher;
use Quotebot\Infrastructure\ProposalPublisher\QuoteProposalPublisher;
use Quotebot\Infrastructure\SystemTimeService;

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
        $projectRoot = __DIR__ . '/../../..';
        if (file_exists($projectRoot. '/.env')) {
            $dotenv = Dotenv::createImmutable($projectRoot);
            $dotenv->load();
        }

        $environment = $_ENV['APP_ENV'] ?? 'PROD';

        if ($environment === 'LOCAL') {
            $proposalPublisher = new LocalQuoteProposalPublisher();
            $adSpaceProvider = new LocalAdSpaceProvider();
            $marketDataRetriever = new LocalMarketDataRetriever();
        } else {
            $proposalPublisher = new QuoteProposalPublisher();
            $adSpaceProvider = new BlogAdSpaceProvider();
            $marketDataRetriever = new VendorDataRetriever(new MarketStudyVendor());

        }

        $timeService = new SystemTimeService();

        $calculateProposal = new CalculateProposal($timeService);

        $blogAuctionTask = new BlogAuctionTask(
            $marketDataRetriever,
            $proposalPublisher,
            $calculateProposal
        );


        self::$bot = self::$bot ?? new AutomaticQuoteBot(
                $blogAuctionTask,
                $adSpaceProvider
            );
        self::$bot->sendAllQuotes('FAST');
    }
}
