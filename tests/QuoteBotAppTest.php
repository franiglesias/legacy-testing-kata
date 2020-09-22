<?php

namespace Tests\Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\AutomaticQuoteBot;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Domain\MarketDataRetriever;
use Quotebot\Domain\Price;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Domain\TimeService;
use Quotebot\Infrastructure\EntryPoint\Application;

class QuoteBotAppTest extends TestCase
{
    public function testShouldRun(): void
    {
        $marketStudyVendor = $this->createMock(MarketDataRetriever::class);
        $marketStudyVendor->method('averagePrice')->willReturn(new Price(0));

        $proposalPublisher = $this->createMock(ProposalPublisher::class);
        $calculateProposal = new CalculateProposal($this->createMock(TimeService::class));

        $blogAuctionTask = new BlogAuctionTask(
            $marketStudyVendor,
            $proposalPublisher,
            $calculateProposal
        );

        $adSpaceProvider = $this->createMock(AdSpaceProvider::class);

        $adSpaceProvider->method('getSpaces')->willReturn(['Blog1', 'Blog2']);
        $automaticQuoteBot = new AutomaticQuoteBot(
            $blogAuctionTask,
            $adSpaceProvider
        );

        Application::inject($automaticQuoteBot);
        Application::main();

        self::assertTrue(true);
    }

}
