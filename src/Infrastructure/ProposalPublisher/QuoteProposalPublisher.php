<?php


namespace Quotebot\Infrastructure\ProposalPublisher;


use Quotebot\Domain\Proposal\Proposal;
use Quotebot\Domain\ProposalPublisher;

class QuoteProposalPublisher implements ProposalPublisher
{

    public function __construct()
    {
    }

    public function publish(Proposal $proposal): void
    {
        \QuotePublisher::publish($proposal->getProposal());
    }
}