<?php

namespace Quotebot\Domain;

final class BlogAuctionTask
{

    protected Publisher $publisher;
    private ProposalBuilder $proposalBuilder;
    private Printer $printer;

    public function __construct(
        Publisher $publisher,
        ProposalBuilder $proposalBuilder,
        Printer $printer
    ) {
        $this->publisher = $publisher;
        $this->proposalBuilder = $proposalBuilder;
        $this->printer = $printer;
    }

    public function priceAndPublish(Blog $blog, Mode $mode): void
    {
        $proposal = $this->calculateProposal($blog, $mode);

        $this->publishProposal($proposal);
    }

    private function publishProposal(Proposal $proposal): void
    {
        $this->printer->print($proposal);
        $this->publisher->publish($proposal);
    }

    private function calculateProposal(Blog $blog, Mode $mode): Proposal
    {
        return $this->proposalBuilder->calculateProposal($blog, $mode);
    }
}
