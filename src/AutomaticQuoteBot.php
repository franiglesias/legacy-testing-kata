<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Infrastructure\Clock\SystemClock;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs();
        $blogAuctionTask = $this->buildBlogAuctionTask();

        foreach ($blogs as $blog) {
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs()
    {
        return AdSpace::getAdSpaces();
    }

    private function buildBlogAuctionTask(): BlogAuctionTask
    {
        $marketStudyProvider = new MarketStudyVendorAdapter(new MarketStudyVendor());
        $publisher = new VendorPublisher();
        $clock = new SystemClock();
        $proposalBuilder = new ProposalBuilder($marketStudyProvider, $clock);

        return new BlogAuctionTask($marketStudyProvider, $publisher, $clock, $proposalBuilder);
    }
}
