<?php

namespace Quotebot;

use Quotebot\Domain\AdSpaceRepository;

class AutomaticQuoteBot
{

    private BlogAuctionTask $blogAuctionTask;
    private AdSpaceRepository $adSpaceRepository;

    public function __construct(
        BlogAuctionTask $blogAuctionTask,
        AdSpaceRepository $adSpaceRepository
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

    protected function getBlogs(): array
    {
        return $this->adSpaceRepository->findAll();
    }
}
