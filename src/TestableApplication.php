<?php

namespace Quotebot;

use PHPUnit\Framework\TestCase;

class ApplicationTestCase extends TestCase
{
    private $publisher;

    /** main application method */
    public function testMain()
    {
        $this->publisher = new SpyPublisher();
        $bot = new AutomaticQuoteBot(
            new BlogAuctionTask(
                new MarketStudy(),
                $this->publisher,
                new TimeService()
            )
        );
        $bot->sendAllQuotes('FAST');

        self::assertEquals(5, $this->publisher->proposals());
    }
}
