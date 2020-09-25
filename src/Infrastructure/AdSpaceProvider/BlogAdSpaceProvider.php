<?php


namespace Quotebot\Infrastructure\AdSpaceProvider;


use Quotebot\Domain\AdSpace\Blog;
use Quotebot\Domain\AdSpaceProvider;

class BlogAdSpaceProvider implements AdSpaceProvider
{

    public function getSpaces(): array
    {
        return $this->retrieveSpaces();
    }

    private function retrieveSpaces(): array
    {
        $blogs = AdSpacesCache::getAdSpaces('blogs');

        if ($blogs) {
            return $blogs;
        }

        $rawData = TechBlogs::listAllBlogs();

        $blogs = array_map(static function ($space) {
            return new Blog($space);
        }, $rawData);

        AdSpacesCache::cache('blogs', $blogs);
    }
}