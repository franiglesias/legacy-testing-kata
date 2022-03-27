<?php
declare(strict_types=1);

namespace Quotebot\Application;


use Quotebot\BlogAuctionTask;
use Quotebot\Domain\GetAdSpaces;
use Quotebot\Domain\Mode;

class SendAllQuotesHandler
{
	private BlogAuctionTask $blogAuctionTask;
	private GetAdSpaces $getAdSpaces;

	public function __construct(BlogAuctionTask $blogAuctionTask, GetAdSpaces $getAdSpaces)
	{
		$this->blogAuctionTask = $blogAuctionTask;
		$this->getAdSpaces     = $getAdSpaces;
	}

	public function __invoke(SendAllQuotes $sendAllQuotes): void
	{
		$this->sendAllQuotes($sendAllQuotes->getRawMode());
	}

	private function sendAllQuotes(string $rawMode): void
	{
		$mode = new Mode($rawMode);

		$blogs = $this->getBlogs();
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}

	protected function getBlogs()
	{
		return $this->getAdSpaces->all();
	}
}
