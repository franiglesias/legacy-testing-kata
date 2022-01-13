<?php

namespace Quotebot\Domain;

final class BlogAuctionTask
{

    protected Publisher $publisher;
    private ProposalBuilder $proposalBuilder;
    private Writer $writer;

    public function __construct(
        Publisher $publisher,
        ProposalBuilder $proposalBuilder,
        Writer $writer
    ) {
        $this->publisher = $publisher;
        $this->proposalBuilder = $proposalBuilder;
        $this->writer = $writer;
    }

    public function priceAndPublish(Blog $blog, Mode $mode): void
    {
        $proposal = $this->calculateProposal($blog, $mode);
        $this->showProposal($blog, $mode, $proposal);
        $this->publishProposal($proposal);
    }

    private function publishProposal(Proposal $proposal): void
    {
        $this->publisher->publish($proposal);
    }

    private function calculateProposal(Blog $blog, Mode $mode): Proposal
    {
        return $this->proposalBuilder->calculateProposal($blog, $mode);
    }

    protected function showProposal(Blog $blog, Mode $mode, Proposal $proposal): void
    {
        $theLine = sprintf('%s (%s) %s', $blog->name(), $mode, $proposal->amount());
        $this->writer->line($theLine);
    }
}
