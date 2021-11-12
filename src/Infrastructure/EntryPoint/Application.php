<?php

namespace Quotebot\Infrastructure\EntryPoint;

use Quotebot\AutomaticQuoteBot;
use Symfony\Component\Dotenv\Dotenv;

class Application
{
    /** main application method */
    public static function main(array $args = null)
    {
        $path = $args['BASE_PATH'] ?? __DIR__ . '/../../../';
        $dotEnvFile = $path . '.env';

        if (file_exists($dotEnvFile)) {
            (new Dotenv())->loadEnv($dotEnvFile, 'APP_ENV');
        }

        $environment = $args['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'prod';

        if ($environment === 'prod') {
            $bot = new AutomaticQuoteBot();
            $bot->sendAllQuotes('FAST');
        }
    }
}
