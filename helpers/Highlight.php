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
class Highlight
{
    /**
     * Escape html special characters in the text
     *
     * @param string $text
     * @return string
     */
    public static function escape($text)
    {
        return \htmlspecialchars($text, \ENT_COMPAT, 'UTF-8');
    }

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
            $text = self::escape($text);
        }
        return $text;
    }

    /**
     * Create HTML-header
     *
     * @param \axy\ml\helpers\Token $token
     * @param array $options
     * @return string
     */
    public static function header(Token $token, $options)
    {
        if ($options['hHandler']) {
            return Callback::call($options['hHandler'], [$token]);
        }
        $level = $token->level + $options['hStart'] - 1;
        if ($level > 6) {
            $level = 6;
        }
        if (isset($token->name) && ($token->name !== '')) {
            $attr = ' id="'.self::escape($token->name).'"';
        } else {
            $attr = '';
        }
        return '<h'.$level.$attr.'>'.self::text($token->content, $options).'</h'.$level.'>';
    }
}
