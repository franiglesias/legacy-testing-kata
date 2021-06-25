<?php
declare(strict_types=1);

namespace Quotebot\Application;


use Quotebot\AutomaticQuoteBot;

class SendAllQuotesHandler
{

	private AutomaticQuoteBot $automaticQuoteBot;

	public function __construct(AutomaticQuoteBot $automaticQuoteBot)
	{
		$this->automaticQuoteBot = $automaticQuoteBot;
	}

	public function __invoke(SendAllQuotes $sendAllQuotes): void
	{
		$this->automaticQuoteBot->sendAllQuotes($sendAllQuotes->getMode());
	}
}
