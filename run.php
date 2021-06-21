<?php

require 'vendor/autoload.php';

use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Infrastructure\VendorMarketDataProvider;
use Quotebot\Infrastructure\VendorQuotePublisher;

$marketDataProvider = new VendorMarketDataProvider(new MarketStudyVendor);
$publisher = new VendorQuotePublisher;
$blogAuctionTask    = new BlogAuctionTask($marketDataProvider, $publisher);
$automaticQuoteBot  = new AutomaticQuoteBot($blogAuctionTask);

Application::injectBot($automaticQuoteBot);
Application::main();
