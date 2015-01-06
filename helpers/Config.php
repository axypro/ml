<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

/**
 * Access to ml-configs (default options, tags)
 */
class Config
{
    /**
     * Returns the list of default options
     *
     * @return array
     */
    public static function getOptions()
    {
        return self::getConfig('options');
    }

    /**
     * Returns the list of standard tags
     *
     * @return array
     */
    public static function getTags()
    {
        return self::getConfig('tags');
    }

    /**
     * Returns a system config by a name
     *
     * @param string $name
     * @return array
     */
    private static function getConfig($name)
    {
        if (!isset(self::$configs[$name])) {
            $filename = __DIR__.'/'.self::$dir.'/'.$name.'.php';
            self::$configs[$name] = include $filename;
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
