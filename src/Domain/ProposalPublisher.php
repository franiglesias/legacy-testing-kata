<?php


namespace Quotebot\Domain;


use Quotebot\Domain\Proposal\Proposal;

interface ProposalPublisher
{
    public function publish(Proposal $proposal): void;
}