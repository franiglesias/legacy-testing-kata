<?php


namespace Quotebot\Application;


use Quotebot\Domain\AdSpace\AdSpace;
use Quotebot\Domain\AdSpaceProvider;
use Quotebot\Domain\Proposal\GenerateProposal;
use Quotebot\Domain\Proposal\Mode;
use Quotebot\Domain\ProposalPublisher;

class GenerateAllQuotesCommandHandler
{
    /**
     * @var GenerateProposal
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

    public function __construct(GenerateProposal $blogAuctionTask,
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
        $blogs = $this->adSpaceProvider->findSpaces($generateAllQuotes->getSpecification());
        foreach ($blogs as $blog) {
            $proposal = $this->blogAuctionTask->forAdSpace($blog, $mode);
            $this->proposalPublisher->publish($proposal);
        }
    }
}
