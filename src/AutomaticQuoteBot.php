<?php

namespace Quotebot;


class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode)
    {
        $blogs = AdSpace::getAdSpaces($mode);
        foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask();
            $blogAuctionTask->PriceAndPublish($blog, $mode);
        }
    }
}