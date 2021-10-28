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
        $publisher = new VendorPublisher();

        $proposalBuilder = $this->buildProposalBuilder();

        return new BlogAuctionTask($publisher, $proposalBuilder);
    }

    private function buildProposalBuilder(): ProposalBuilder
    {
        $marketStudyProvider = new MarketStudyVendorAdapter(new MarketStudyVendor());
        $clock = new SystemClock();

        return new ProposalBuilder($marketStudyProvider, $clock);
    }
}
