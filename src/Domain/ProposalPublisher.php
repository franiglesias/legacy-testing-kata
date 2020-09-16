<?php


namespace Quotebot\Domain;


interface ProposalPublisher
{
    public function publish(float $proposal): void;
}