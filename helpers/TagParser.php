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

    /**
     * Load the last component from the tag value
     *
     * @param string $content
     * @return string
     */
    public static function loadLastComponent($content)
    {
        $content = \trim($content);
        if (empty($content)) {
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

    /**
     * Get creating parameters from tag list
     *
     * @param mixed $params
     * @return array (classname, options)
     */
    public static function getParamsFromTagList($params)
    {
        if (\is_array($params)) {
            $result = [
                'classname' => isset($params['classname']) ? $params['classname'] : null,
                'options' => isset($params['options']) ? $params['options'] : [],
            ];
        } else {
            $result = [
                'classname' => $params,
                'options' => [],
            ];
        }
        if (($result['classname']) && ($result['classname'][0] !== '\\')) {
            $result['classname'] = '\axy\ml\tags\\'.$result['classname'];
        }
        return $result;
    }

    /**
     * Merge the default list of tags with a custom list
     *
     * @param array $default
     * @param array $custom
     * @return array
     */
    public static function mergeTagLists(array $default, array $custom = null)
    {
        foreach ($default as &$params) {
            $params = self::getParamsFromTagList($params);
        }
        unset($params);
        if ($custom) {
            foreach ($custom as $k => $params) {
                if ($params) {
                    $params = self::getParamsFromTagList($params);
                    if (isset($default[$k])) {
                        if ($params['classname']) {
                            $default[$k] = $params['classname'];
                        }
                        $default[$k]['options'] = \array_replace($default[$k]['options'], $params['options']);
                    } else {
                        $default[$k] = $params;
                    }
                } else {
                    $default[$k] = null;
                }
            }
        }
        $result = [];
        foreach ($default as $k => $params) {
            if ($params && $params['classname']) {
                $result[$k] = $params;
            }
        }
        return $result;
    }
}
