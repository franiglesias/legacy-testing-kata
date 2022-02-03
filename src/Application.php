<?php

namespace Quotebot;

class Application
{
	/** main application method */
	public static function main(array $args = null): void
	{
		$bot = new AutomaticQuoteBot(
			new VendorMarketData(new \MarketStudyVendor),
			new VendorPublisher,
			new SystemClock
		);
		$bot->sendAllQuotes('FAST');
	}
}
