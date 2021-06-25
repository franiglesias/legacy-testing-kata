<?php

namespace Quotebot;

class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(?BlogAuctionTask $blogAuctionTask = null)
	{
		$this->blogAuctionTask = $blogAuctionTask ?? new BlogAuctionTask;
	}

	public function sendAllQuotes(string $mode): void
    {
		$blogs = $this->retrieveBlogs();
		foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

	protected function retrieveBlogs()
	{
		return AdSpace::getAdSpaces();
	}
}
