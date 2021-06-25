<?php
declare(strict_types=1);

namespace Quotebot\Tests\E2e;

use PHPUnit\Framework\TestCase;
use Quotebot\Application;
use Quotebot\Application\SendAllQuotesHandler;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Domain\ClockService;
use Quotebot\Domain\GetAdSpaces;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Tests\E2e\Doubles\PublisherSpy;

class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketDataProvider = $this->createMock(MarketDataProvider::class);
		$marketDataProvider->method('averagePrice')->willReturn(0.0);

		$publisherSpy = new PublisherSpy;

		$clockService = $this->createMock(ClockService::class);
		$clockService->method('timestampDiff')->willReturn(0);

		$calculateProposal = new CalculateProposal($clockService);

		$blogAuctionTask = new BlogAuctionTask(
			$marketDataProvider,
			$publisherSpy,
			$calculateProposal
		);
		$quoteBot        = $this->buildSendAllQuotesHandler($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $publisherSpy->proposals());
	}

	private function buildSendAllQuotesHandler(BlogAuctionTask $blogAuctionTask, array $blogs): SendAllQuotesHandler
	{
		$getAdSpaces = $this->createMock(GetAdSpaces::class);
		$getAdSpaces->method('all')->willReturn($blogs);

		return new SendAllQuotesHandler($blogAuctionTask, $getAdSpaces);
	}
}
