<?php

namespace Quotebot;

use Quotebot\Domain\Mode;

class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(BlogAuctionTask $blogAuctionTask)
	{
		$this->blogAuctionTask = $blogAuctionTask;
	}

	public function sendAllQuotes(string $rawMode): void
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
