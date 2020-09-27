<?php


namespace Quotebot\Application;


use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\Proposal\Mode;
use Quotebot\Domain\ProposalPublisher;

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
    /**
     * @var ProposalPublisher
     */
    private $proposalPublisher;

    public function __construct(BlogAuctionTask $blogAuctionTask,
                                AdSpaceProvider $adSpaceProvider,
                                ProposalPublisher $proposalPublisher
)
    {

        $this->blogAuctionTask = $blogAuctionTask;
        $this->adSpaceProvider = $adSpaceProvider;
        $this->proposalPublisher = $proposalPublisher;
    }

    public function __invoke(GenerateAllQuotes $generateAllQuotes): void
    {
        $mode = new Mode($generateAllQuotes->getRawMode());
        $blogs = $this->adSpaceProvider->getSpaces();
        foreach ($blogs as $blog) {
            $proposal = $this->blogAuctionTask->generateProposal($blog, $mode);
            $this->proposalPublisher->publish($proposal);
        }
    }
}
