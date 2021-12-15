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

    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs();

        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish(new Blog($blog), new Mode($mode));
        }
    }

    protected function getBlogs(): array
    {
        return $this->adSpaceRepository->findAll();
    }
}
