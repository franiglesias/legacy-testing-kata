<?php

namespace Quotebot;

class AutomaticQuoteBot
{
	private $blogAuctionTask;

	public function __construct($marketData, $publisher, $clock)
	{
		$this->blogAuctionTask = new BlogAuctionTask(
			$marketData,
			$publisher,
			$clock
		);
	}

	public function sendAllQuotes(string $mode): void
	{
		$blogs = AdSpace::getAdSpaces($mode);

		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}
}
