<?php

namespace Quotebot;

use Quotebot\Domain\AdSpaceProvider;

class AutomaticQuoteBot
{
    private $blogAuctionTask;
    /**
     * @var AdSpaceProvider
     */
    private $adSpaceProvider;

    public function __construct(
        BlogAuctionTask $blogAuctionTask,
        AdSpaceProvider $adSpaceProvider
    )
    {
        $this->blogAuctionTask = $blogAuctionTask;
        $this->adSpaceProvider = $adSpaceProvider;
    }

    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs($mode);
        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs(string $mode)
    {
        return $this->adSpaceProvider->getSpaces();
    }
}
