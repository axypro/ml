<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

/**
 * Text format normalizer
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Normalizer
{
    /**
     * Normalize the text to parse
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function toParse($content, $options)
    {
        $tab = \str_repeat(' ', $options['tab']);
        $content = \str_replace("\t", $tab, $content);
        return \preg_replace('/(\r\n)|(\n\r)|\n|\r/', "\n", $content);
    }

    /**
     * Normalize the text to result
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function toResult($content, $options)
    {
        $nl = $options['nl'];
        if ($nl !== "\n") {
            $content = \str_replace("\n", $nl, $content);
        }
        return $content;
    }
}
