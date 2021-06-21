<?php
declare(strict_types=1);

namespace Quotebot\Tests\E2e;

use MarketStudyVendor;
use PHPUnit\Framework\TestCase;
use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;

class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketStudyVendor = $this->createMock(MarketStudyVendor::class);
		$marketStudyVendor->method('averagePrice')->willReturn(0.0);

		$blogAuctionTask = $this->buildBlogAuctionTask($marketStudyVendor);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $blogAuctionTask->proposals);
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

			protected function getBlogs(string $mode): array
			{
				return $this->blogs;
			}
		};
	}

	private function buildBlogAuctionTask(MarketStudyVendor $marketStudyVendor): BlogAuctionTask
	{
		return new class($marketStudyVendor) extends BlogAuctionTask {
			public array $proposals = [];

			protected function publishProposal($proposal): void
			{
				$this->proposals[] = $proposal;
			}
		};
	}
}
