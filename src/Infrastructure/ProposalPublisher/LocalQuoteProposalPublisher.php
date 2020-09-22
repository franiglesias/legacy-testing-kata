<?php


namespace Quotebot\Infrastructure\ProposalPublisher;


use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalPublisher;

class LocalQuoteProposalPublisher implements ProposalPublisher
{

    public function __construct()
    {
    }

    public function publish(Proposal $proposal): void
    {
        printf('Local execution. Proposal of %s created, but it wasn\'t sent.'.chr(10), $proposal->getProposal());
    }
}