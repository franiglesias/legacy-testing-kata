<?php

namespace Quotebot;

class Application
{
	private static ?AutomaticQuoteBot $quoteBot;

	public static function injectBot(AutomaticQuoteBot $quoteBot): void
	{
		self::$quoteBot = $quoteBot;
	}

	/** main application method */
    public static function main(array $args = null): void
	{
    	if (null === self::$quoteBot) {
    		self::$quoteBot = new AutomaticQuoteBot();
		}

        self::$quoteBot->sendAllQuotes('FAST');
    }
}
