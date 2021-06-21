<?php
declare(strict_types=1);

namespace Quotebot\Tests\E2e;

use PHPUnit\Framework\TestCase;
use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\ClockService;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Mode;
use Quotebot\Tests\E2e\Doubles\PublisherSpy;

class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketDataProvider = $this->createMock(MarketDataProvider::class);
		$marketDataProvider->method('averagePrice')->willReturn(0.0);

		$publisherSpy    = new PublisherSpy;

		$clockService    = $this->createMock(ClockService::class);
		$clockService->method('timestampDiff')->willReturn(0);

		$blogAuctionTask = new BlogAuctionTask(
			$marketDataProvider,
			$publisherSpy,
			$clockService
		);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $publisherSpy->proposals());
	}

	private function buildQuoteBot(BlogAuctionTask $blogAuctionTask, array $blogs): AutomaticQuoteBot
	{
		return new class($blogAuctionTask, $blogs) extends AutomaticQuoteBot {
			private array $blogs;

			public function __construct(BlogAuctionTask $blogAuctionTask, array $blogs)
			{
				$this->blogs = $blogs;
				parent::__construct($blogAuctionTask);
			}

			protected function getBlogs(Mode $mode): array
			{
				return $this->blogs;
			}
		};
	}
}
