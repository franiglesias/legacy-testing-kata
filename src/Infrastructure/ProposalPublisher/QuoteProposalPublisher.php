<?php


namespace Quotebot\Infrastructure;


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