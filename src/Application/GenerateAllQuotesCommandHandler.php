<?php


namespace Quotebot\Application;


use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\Proposal\Mode;

class GenerateAllQuotesCommandHandler
{
    /**
     * @var BlogAuctionTask
     */
    private $blogAuctionTask;
    /**
     * @var AdSpaceProvider
     */
    private $adSpaceProvider;

    public function __construct(BlogAuctionTask $blogAuctionTask,
                                AdSpaceProvider $adSpaceProvider)
    {

        $this->blogAuctionTask = $blogAuctionTask;
        $this->adSpaceProvider = $adSpaceProvider;
    }

    public function __invoke(GenerateAllQuotes $generateAllQuotes): void
    {
        $mode = new Mode($generateAllQuotes->getRawMode());
        $blogs = $this->adSpaceProvider->getSpaces();
        foreach ($blogs as $blog) {
            $this->blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }
}
