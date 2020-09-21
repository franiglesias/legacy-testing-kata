<?php


namespace Quotebot\Infrastructure\ProposalPublisher;


use Quotebot\Domain\ProposalPublisher;

class QuoteProposalPublisher implements ProposalPublisher
{

    public function __construct()
    {
    }

    public function publish(float $proposal): void
    {
        \QuotePublisher::publish($proposal);
    }
}