<?php


namespace Quotebot\Domain;


interface ProposalPublisher
{
    public function publish(Proposal $proposal): void;
}