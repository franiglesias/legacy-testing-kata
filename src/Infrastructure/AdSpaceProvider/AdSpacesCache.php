<?php

namespace Quotebot\Infrastructure\AdSpaceProvider;

class AdSpacesCache
{
    private static $cache = [];

    /** Get all ad spaces
     * @param $key
     * @return array|mixed
     */
    public static function getAdSpaces($key)
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        return [];
    }

    public static function cache(string $key, array $elements): void
    {
        static::$cache[$key]= $elements;
    }

}
