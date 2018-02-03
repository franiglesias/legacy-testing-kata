<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 1/2/18
 * Time: 21:46
 */

namespace Quotebot;


class TechBlogs
{
    public static function listAllBlogs() : array
    {
        try {
            // Access to DB is very slow
            sleep(5);
        } catch (\RuntimeException $exception) {
            throw new \RuntimeException('Unexpected exception');
        }

        return [
            'HackerNews',
            'Reddit',
            'TechCrunch',
            'BuzzFeed',
            'TMZ',
            'TheHuffPost',
            'GigaOM'
        ];
    }
}