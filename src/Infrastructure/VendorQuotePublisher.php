<?php
declare(strict_types=1);

namespace Quotebot\Infrastructure;


use Quotebot\Domain\Publisher;
use QuotePublisher;

class VendorQuotePublisher implements Publisher
{

	public function publishProposal($proposal): void
	{
		QuotePublisher::publish($proposal);
	}
}
