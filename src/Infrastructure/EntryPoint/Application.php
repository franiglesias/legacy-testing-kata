<?php

namespace Quotebot\Infrastructure\EntryPoint;

use Quotebot\AutomaticQuoteBot;
use Quotebot\Infrastructure\AdSpaces\VendorAdSpacesRepository;
use Quotebot\Infrastructure\Builder\BlogAuctionTaskBuilder;
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
            $blogAuctionTaskBuilder = new BlogAuctionTaskBuilder();
            $blogAuctionTask = $blogAuctionTaskBuilder->buildBlogAuctionTask();

            $adSpaceRepository = new VendorAdSpacesRepository();

            $bot = new AutomaticQuoteBot($blogAuctionTask, $adSpaceRepository);
            $bot->sendAllQuotes('FAST');
        }
    }
}
