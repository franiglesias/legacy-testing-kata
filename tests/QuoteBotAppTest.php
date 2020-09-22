<?php

namespace Tests\Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Application\AutomaticQuoteBot;
use Quotebot\Application\BlogAuctionTask;
use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\MarketData\MarketDataRetriever;
use Quotebot\Domain\MarketData\Price;
use Quotebot\Domain\Proposal\CalculateProposal;
use Quotebot\Domain\Proposal\TimeService;
use Quotebot\Domain\ProposalPublisher;
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
