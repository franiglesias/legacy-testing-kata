<?php

namespace Quotebot;

class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask();
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

	protected function getBlogs(string $mode)
	{
		return AdSpace::getAdSpaces($mode);
	}
}
