<?php
declare(strict_types=1);

namespace Quotebot\Test\Application;

use Quotebot\Application\SendAllQuotes;
use PHPUnit\Framework\TestCase;
use Quotebot\Application\SendAllQuotesHandler;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;

class SendAllQuotesTest extends TestCase
{
	/** @test */
	public function shouldSendQuotesToEachBlog(): void
	{
		$sendAllQuotes = new SendAllQuotes('FAST');

		$blogAuctionTask = $this->getBlogAuctionTask();

		$automaticQuoteBot = $this->getAutomaticQuoteBot($blogAuctionTask);

		$sendAllQuotesUseCase = new SendAllQuotesHandler($automaticQuoteBot);

		$sendAllQuotesUseCase->__invoke($sendAllQuotes);

		self::assertCount(2, $blogAuctionTask->proposals);
	}

	private function getAutomaticQuoteBot(BlogAuctionTask $blogAuctionTask): AutomaticQuoteBot
	{
		$automaticQuoteBot = new class($blogAuctionTask) extends AutomaticQuoteBot{
			protected function retrieveBlogs(): array
			{
				return ['Blog 1', 'Blog 2'];
			}

		};

		return $automaticQuoteBot;
	}

	private function getBlogAuctionTask(): BlogAuctionTask
	{
		$blogAuctionTask = new class() extends BlogAuctionTask {
			public $proposals = [];
			protected function averagePrice(string $blog): float
			{
				return 0.0;
			}

			protected function publishProposal($proposal): void
			{

				$this->proposals[] = $proposal;
			}

		};

		return $blogAuctionTask;
	}

}
