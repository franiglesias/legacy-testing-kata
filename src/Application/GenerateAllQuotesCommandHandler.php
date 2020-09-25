<?php


namespace Quotebot\Application;


class GenerateAllQuotesCommandHandler
{
    /**
     * @var AutomaticQuoteBot
     */
    private $automaticQuoteBot;

    public function __construct(AutomaticQuoteBot $automaticQuoteBot)
    {
        $this->automaticQuoteBot = $automaticQuoteBot;
    }

    public function __invoke(GenerateAllQuotes $generateAllQuotes): void
    {
        $this->automaticQuoteBot->sendAllQuotes($generateAllQuotes->getRawMode());
    }

}