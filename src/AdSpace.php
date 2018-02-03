<?php
/**
 * Created by PhpStorm.
 * User: frankie
 * Date: 1/2/18
 * Time: 21:52
 */

namespace Quotebot;


class AdSpace
{
    private static $cache = [];

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