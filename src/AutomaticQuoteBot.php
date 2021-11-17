<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Infrastructure\Clock\SystemClock;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class AutomaticQuoteBot
{

    private ?BlogAuctionTask $blogAuctionTask;

    public function __construct(?BlogAuctionTask $blogAuctionTask = null)
    {
        $this->blogAuctionTask = $blogAuctionTask ?? $this->buildBlogAuctionTask();
    }

    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs();

        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
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
