<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\AdSpaceRepository;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Infrastructure\Builder\BlogAuctionTaskBuilder;
use Quotebot\Infrastructure\Clock\SystemClock;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class AutomaticQuoteBot
{

    private ?BlogAuctionTask $blogAuctionTask;
    private ?AdSpaceRepository $adSpaceRepository;

    public function __construct(
        BlogAuctionTask $blogAuctionTask,
        ?AdSpaceRepository $adSpaceRepository = null
    ) {
        $this->blogAuctionTask = $blogAuctionTask;
        $this->adSpaceRepository = $adSpaceRepository;
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
        if ($this->adSpaceRepository) {
            return $this->adSpaceRepository->findAll();
        }

        return AdSpace::getAdSpaces();
    }
}
