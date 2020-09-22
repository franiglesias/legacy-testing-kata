<?php

namespace Quotebot\Application;

use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\Proposal\Mode;

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

    public function sendAllQuotes(string $rawMode): void
    {
        $mode = new Mode($rawMode);
        $blogs = $this->getBlogs();
        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs()
    {
        return $this->adSpaceProvider->getSpaces();
    }
}
