<?php

namespace Quotebot;

class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = $this->getBlogs();
        $blogAuctionTask = new BlogAuctionTask();

        foreach ($blogs as $blog) {
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

    protected function getBlogs()
    {
        return AdSpace::getAdSpaces();
    }
}
