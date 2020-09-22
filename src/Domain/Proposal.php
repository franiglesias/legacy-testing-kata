<?php


namespace Quotebot\Domain;


class Proposal
{
    /**
     * @var float
     */
    private $proposal;

    public function __construct(float $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * @return float
     */
    public function getProposal(): float
    {
        return $this->proposal;
    }
}