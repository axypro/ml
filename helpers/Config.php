<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * Load ml-configs (default options, tags)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Config
{
    /**
     * @return array
     */
    public static function getOptions()
    {
        if (!self::$options) {
            $filename = __DIR__.'/'.self::$dir.'/options.php';
            self::$options = include($filename);
        }
        return self::$options;
    }

    /**
     * @return array
     */
    public static function getTags()
    {
        if (!self::$tags) {
            $filename = __DIR__.'/'.self::$dir.'/tags.php';
            self::$tags = include($filename);
        }
        return self::$tags;
    }

    /**
     * @var string
     */
    private static $dir = '../config';

    /**
     * @var array
     */
    private static $options;

    /**
     * @var array
     */
    private static $tags;
}
