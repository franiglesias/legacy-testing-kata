<?php
declare(strict_types=1);

namespace Quotebot\Application;


use Quotebot\AdSpace;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\Mode;

class SendAllQuotesHandler
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(BlogAuctionTask $blogAuctionTask)
	{
		$this->blogAuctionTask = $blogAuctionTask;
	}

	public function __invoke(SendAllQuotes $sendAllQuotes): void
	{
		$this->sendAllQuotes($sendAllQuotes->getRawMode());
	}

	private function sendAllQuotes(string $rawMode): void
	{
		$mode = new Mode($rawMode);

		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}

	protected function getBlogs(Mode $mode)
	{
		return AdSpace::getAdSpaces((string)$mode);
	}
}
