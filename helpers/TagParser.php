<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * The helper for tag parse
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class TagParser
{
    /**
     * Load the list of attributes from the content
     *
     * @param string &$content
     * @return array
     */
    public static function loadAttrs(&$content)
    {
        if ($content === '') {
            return [];
        }
        if ($content[0] === ':') {
            if (\preg_match('/^\:(\S*)(.*)$/is', $content, $matches)) {
                $attrs = \explode(':', $matches[1]);
                $content = $matches[2];
            } else {
                $attrs = [];
            }
        } else {
            $attrs = [];
        }
        $content = \ltrim($content, ' ');
        if ($content && ($content[0] === "\n")) {
            $content = \substr($content, 1);
        }
        return $attrs;
    }

    /**
     * Load a next component from the tag value
     *
     * @param string $content
     * @return string
     */
    public static function loadNextComponent(&$content)
    {
        $content = \ltrim($content);
        if ($content === '') {
            return '';
        }
        $first = $content[0];
        if (($first === '"') || ($first === "'")) {
            $parts = \explode($first, $content, 3);
            $content = isset($parts[2]) ? \ltrim($parts[2]) : '';
            return $parts[1];
        }
        if (!\preg_match('/^(\S*)\s*(.*)$/is', $content, $matches)) {
            return '';
        }
        $content = $matches[2];
        return $matches[1];
    }

    /**
     * Load the last component from the tag value
     *
     * @param string $content
     * @return string
     */
    public static function loadLastComponent($content)
    {
        $content = \trim($content);
        if ($content === '') {
            return '';
        }
        $first = $content[0];
        if (($first !== '"') && ($first !== "'")) {
            return $content;
        }
        $len = \strlen($content);
        if ($len === 1) {
            return $content;
        }
        if ($content[$len - 1] !== $first) {
            return $content;
        }
        return \substr($content, 1, -1);
    }
}
