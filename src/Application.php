<?php

namespace Quotebot;

use Quotebot\Application\SendAllQuotes;
use Quotebot\Application\SendAllQuotesHandler;

class Application
{
	private static ?SendAllQuotesHandler $handler;

	public static function injectBot(SendAllQuotesHandler $handler): void
	{
		self::$handler = $handler;
	}

	/** main application method */
    public static function main(array $args = null): void
	{
    	if (null === self::$handler) {
    		self::$handler = new SendAllQuotesHandler();
		}

        (self::$handler)(new SendAllQuotes('FAST'));
    }
}
