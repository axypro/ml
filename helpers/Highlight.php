<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

use axy\callbacks\Callback;

/**
 * The helper for handling and wrapping blocks and values
 */
class Highlight
{
    /**
     * Escapes html special characters in the text
     *
     * @param string $text
     * @return string
     */
    public static function escape($text)
    {
        return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Escapes the plain text
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function text($text, $options)
    {
        if ($options['textHandler']) {
            $params = (object)[
                'content' => $text,
                'escape' => $options['escape'],
            ];
            Callback::call($options['textHandler'], [$params]);
            $text = $params->content;
            if ($params->escape) {
                $text = self::escape($text);
            }
        } elseif ($options['escape']) {
            $text = self::escape($text);
        }
        return $text;
    }

    /**
     * Creates HTML-header
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
        if (isset($token->link) && ($token->link !== '')) {
            $attr = ' id="'.self::escape($token->link).'"';
        } else {
            $attr = '';
        }
        return '<h'.$level.$attr.'>'.self::text($token->content, $options).'</h'.$level.'>';
    }
}
