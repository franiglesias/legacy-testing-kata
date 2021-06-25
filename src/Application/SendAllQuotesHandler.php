<?php
declare(strict_types=1);

namespace Quotebot\Application;


use Quotebot\AutomaticQuoteBot;
use Quotebot\Domain\Mode;

class SendAllQuotesHandler
{

	private AutomaticQuoteBot $automaticQuoteBot;

	public function __construct(AutomaticQuoteBot $automaticQuoteBot)
	{
		$this->automaticQuoteBot = $automaticQuoteBot;
	}

	public function __invoke(SendAllQuotes $sendAllQuotes): void
	{
		$mode = new Mode($sendAllQuotes->getMode());

		$this->automaticQuoteBot->sendAllQuotes($mode);
	}
}
