<?php

namespace Quotebot\Domain;

use Quotebot\Infrastructure\Printer\ConsolePrinter;

final class BlogAuctionTask
{

    protected Publisher $publisher;
    private ProposalBuilder $proposalBuilder;
    private ConsolePrinter $printer;

    public function __construct(
        Publisher $publisher,
        ProposalBuilder $proposalBuilder,
        ConsolePrinter $printer
    ) {
        $this->publisher = $publisher;
        $this->proposalBuilder = $proposalBuilder;
        $this->printer = $printer;
    }

    public function priceAndPublish(Blog $blog, Mode $mode): void
    {
        $proposal = $this->calculateProposal($blog, $mode);

        $this->printProposal($proposal);

        $this->publishProposal($proposal);
    }

    private function calculateProposal(Blog $blog, Mode $mode): Proposal
    {
        return $this->proposalBuilder->calculateProposal($blog, $mode);
    }

    private function printProposal(Proposal $proposal): void
    {
        $this->printer->print($proposal);
    }

    private function publishProposal(Proposal $proposal): void
    {
        $this->publisher->publish($proposal);
    }
}
