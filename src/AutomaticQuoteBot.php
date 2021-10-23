<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;

class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs();
        $marketStudyProvider = new MarketStudyVendorAdapter(new MarketStudyVendor());
        $blogAuctionTask = new BlogAuctionTask($marketStudyProvider);

        foreach ($blogs as $blog) {
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs()
    {
        return AdSpace::getAdSpaces();
    }
}
