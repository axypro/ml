<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * Access to ml-configs (default options, tags)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Config
{
    /**
     * Get the list of default options
     *
     * @return array
     */
    public static function getOptions()
    {
        return self::getConfig('options');
    }

    /**
     * Get the list of standard tags
     *
     * @return array
     */
    public static function getTags()
    {
        return self::getConfig('tags');
    }

    /**
     * Get a system config by a name
     *
     * @param string $name
     * @return array
     */
    private static function getConfig($name)
    {
        if (!isset(self::$configs[$name])) {
            $filename = __DIR__.'/'.self::$dir.'/'.$name.'.php';
            self::$configs[$name] = include($filename);
        }
        return self::$configs[$name];
    }

    /**
     * The configs directory (relative to the current)
     *
     * @var string
     */
    private static $dir = '../config';

    /**
     * The cache of configs
     *
     * @var array
     */
    private static $configs = [];
}
