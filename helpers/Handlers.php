<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

use axy\callbacks\Callback;

/**
 * The helper for handling and wrapping blocks and values
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Handlers
{
    /**
     * Escape the plain text
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function text($text, $options)
    {
        if ($options['textHandler']) {
            $text = Callback::call($options['textHandler'], [$text]);
        }
        if ($options['escape']) {
            $text = \htmlspecialchars($text, \ENT_COMPAT, 'UTF-8');
        }
        return $text;
    }

    public static function header(Token $token, $options)
    {

    }
}
