<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\Builder;

use MarketStudyVendor;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\Printer;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Infrastructure\Clock\SystemClock;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class BlogAuctionTaskBuilder
{

    public function buildProposalBuilder(): ProposalBuilder
    {
        $marketStudyProvider = new MarketStudyVendorAdapter(new MarketStudyVendor());
        $clock = new SystemClock();

        return new ProposalBuilder($marketStudyProvider, $clock);
    }

    public function buildBlogAuctionTask(): BlogAuctionTask
    {
        $publisher = new VendorPublisher();

        $proposalBuilder = $this->buildProposalBuilder();

        $printer = new Printer();

        return new BlogAuctionTask($publisher, $proposalBuilder, $printer);
    }
}
