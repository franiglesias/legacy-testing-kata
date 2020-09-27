<?php

namespace Tests\Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Application\GenerateAllQuotesCommandHandler;
use Quotebot\Domain\AdSpace\Blog;
use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\MarketData\MarketDataRetriever;
use Quotebot\Domain\MarketData\Price;
use Quotebot\Domain\Proposal\CalculateProposal;
use Quotebot\Domain\Proposal\GenerateProposal;
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

        $blogAuctionTask = new GenerateProposal(
            $marketStudyVendor, $calculateProposal
        );

        $adSpaceProvider = $this->createMock(AdSpaceProvider::class);
        $adSpaceProvider
            ->method('getSpaces')
            ->willReturn([
                new Blog('Blog1'),
                new Blog('Blog2')
            ]);

        $commandHandler = new GenerateAllQuotesCommandHandler(
            $blogAuctionTask,
            $adSpaceProvider,
            $proposalPublisher
        );

        Application::inject($commandHandler);
        Application::main();

        self::assertTrue(true);
    }

}
