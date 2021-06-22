<?php

require 'vendor/autoload.php';

use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Infrastructure\SystemClockService;
use Quotebot\Infrastructure\VendorMarketDataProvider;
use Quotebot\Infrastructure\VendorQuotePublisher;

$marketDataProvider = new VendorMarketDataProvider(new MarketStudyVendor);
$publisher          = new VendorQuotePublisher;

$clockService      = new SystemClockService;
$calculateProposal = new CalculateProposal($clockService);

$blogAuctionTask   = new BlogAuctionTask(
	$marketDataProvider,
	$publisher,
	$calculateProposal
);
$automaticQuoteBot = new AutomaticQuoteBot($blogAuctionTask);

Application::injectBot($automaticQuoteBot);
Application::main();
