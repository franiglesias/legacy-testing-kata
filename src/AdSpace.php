<?php

namespace Quotebot;

class AdSpace
{
    private static $cache = [];

    /** Get all ad spaces */
    public static function getAdSpaces()
    {
        if (isset(static::$cache['bloglist'])) {
            return static::$cache['bloglist'];
        }

        // FIXME : only return blogs that start with a 'T'

        $listAllBlogs = TechBlogs::listAllBlogs();
        static::$cache['bloglist'] = $listAllBlogs;

        return $listAllBlogs;
    }
}
