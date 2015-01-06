<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

/**
 * Text format normalizer
 */
class Normalizer
{
    /**
     * Normalizes the text before the parsing
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function toParse($content, $options)
    {
        $tab = str_repeat(' ', $options['tab']);
        $content = str_replace("\t", $tab, $content);
        return preg_replace('/(\r\n)|(\n\r)|\n|\r/', "\n", $content);
    }

    /**
     * Normalizes the result text
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function toResult($content, $options)
    {
        $nl = $options['nl'];
        if ($nl !== "\n") {
            $content = str_replace("\n", $nl, $content);
        }
        return $content;
    }
}
