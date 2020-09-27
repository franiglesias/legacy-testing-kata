<?php

namespace Quotebot\Infrastructure\EntryPoint;

use Dotenv\Dotenv;
use MarketStudyVendor;
use Quotebot\Application\GenerateAllQuotes;
use Quotebot\Application\GenerateAllQuotesCommandHandler;
use Quotebot\Domain\AdSpace\AdSpace;
use Quotebot\Domain\Proposal\CalculateProposal;
use Quotebot\Domain\Proposal\GenerateProposal;
use Quotebot\Infrastructure\AdSpaceProvider\BlogAdSpaceProvider;
use Quotebot\Infrastructure\AdSpaceProvider\LocalAdSpaceProvider;
use Quotebot\Infrastructure\MarketDataRetriever\LocalMarketDataRetriever;
use Quotebot\Infrastructure\MarketDataRetriever\VendorDataRetriever;
use Quotebot\Infrastructure\ProposalPublisher\LocalQuoteProposalPublisher;
use Quotebot\Infrastructure\ProposalPublisher\QuoteProposalPublisher;
use Quotebot\Infrastructure\SystemTimeService;

class Application
{
    private static $handler;

    public static function inject(GenerateAllQuotesCommandHandler $handler)
    {
        self::$handler = $handler;
    }

    /** main application method */
    public static function main(array $args = null)
    {
        $projectRoot = __DIR__ . '/../../..';
        if (file_exists($projectRoot . '/.env')) {
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

        $blogAuctionTask = new GenerateProposal(
            $marketDataRetriever, $calculateProposal
        );

        self::$handler = self::$handler ?? new GenerateAllQuotesCommandHandler(
                $blogAuctionTask,
                $adSpaceProvider,
                $proposalPublisher
            );

        $criteria = $_ENV['SPACES_SELECTION'];

        if (!$criteria) {
            $specification = static function (AdSpace $space) {
                return true;
            };
        } else {
            $specification = static function (AdSpace $space) use ($criteria) {
                return $space->startsWith($criteria);
            };
        }

        $generateAllQuotes = new GenerateAllQuotes(
            'FAST',
            $specification
        );

        (self::$handler)($generateAllQuotes);
    }
}
