<?php

namespace Quotebot;

use Quotebot\Domain\AdSpaceRepository;
use Quotebot\Domain\Blog;
use Quotebot\Domain\Mode;

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

    public function sendAllQuotes(string $rawMode): void
    {
        $mode = new Mode($rawMode);

        $blogs = $this->getBlogs();

        foreach ($blogs as $blog) {
            $this->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs(): array
    {
        return $this->adSpaceRepository->findAll();
    }

    private function priceAndPublish($blog, Mode $mode): void
    {
        $this->blogAuctionTask->priceAndPublish($blog, $mode);
    }
}
