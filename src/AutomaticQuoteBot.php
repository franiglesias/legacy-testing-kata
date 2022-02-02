<?php

namespace Quotebot;

class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = AdSpace::getAdSpaces($mode);
        foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask(
				new VendorMarketData(new \MarketStudyVendor()),
				new VendorPublisher(),
				new SystemClock()
			);
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }
}
