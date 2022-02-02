<?php

declare(strict_types=1);

namespace Quotebot;


final class VendorPublisher implements Publisher
{

	public function publish($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}
}
