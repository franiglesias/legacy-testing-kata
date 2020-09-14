<?php

namespace Quotebot;

class AutomaticQuoteBot
{
    private $blogAuctionTask;

    public function __construct(?BlogAuctionTask $blogAuctionTask = null)
    {
        $this->blogAuctionTask = $blogAuctionTask ?? new BlogAuctionTask();
    }

    public function sendAllQuotes(string $mode): void
    {
        $blogs = AdSpace::getAdSpaces($mode);
        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }
}
