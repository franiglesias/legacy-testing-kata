<?php

namespace Tests\Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\AutomaticQuoteBot;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Domain\TimeService;
use Quotebot\Infrastructure\EntryPoint\Application;

class QuoteBotAppTest extends TestCase
{
    public function testShouldRun(): void
    {
        $marketStudyVendor = $this->createMock(\MarketStudyVendor::class);
        $marketStudyVendor->method('averagePrice')->willReturn(0);

        $proposalPublisher = $this->createMock(ProposalPublisher::class);

        $blogAuctionTask = new BlogAuctionTask(
            $marketStudyVendor,
            $proposalPublisher,
            $timeService = $this->createMock(TimeService::class)
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
