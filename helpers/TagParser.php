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
        if (empty($content)) {
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
        if (empty($content)) {
            return '';
        }
        $first = $content[0];
        if (($first === '"') || ($first === "'")) {
            $e = \explode($first, $content, 3);
            $content = isset($e[2]) ? \ltrim($e[2]) : '';
            return $e[1];
        }
        if (!\preg_match('/^(\S*)\s*(.*)$/is', $content, $matches)) {
            return '';
        }
        $content = $matches[2];
        return $matches[1];
    }
}
