<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

use axy\callbacks\Callback;
use axy\ml\Error;

/**
 * The helper for handling and wrapping blocks and values
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Handlers
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
        $attr = empty($token->name) ? '' : ' id="'.self::escape($token->name).'"';
        return '<h'.$level.$attr.'>'.self::text($token->content, $options).'</h'.$level.'>';
    }

    /**
     * Create HTML for a block
     *
     * @param \axy\ml\helpers\Token $container
     * @param array $options
     * @param \axy\ml\TagsList $tags
     * @param array &$errors
     */
    public static function block(Token $container, $options, $tags, &$errors)
    {
        $blocks = [];
        $block = [];
        foreach ($container->subs as $token) {
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $block[] = self::text($token->content, $options);
                    break;
                case Token::TYPE_TAG:
                    $tag = $tags->create($token->name, $token->content);
                    if ($tag) {
                        $block[] = $tag->getHtml();
                        foreach ($tag->getErrors() as $err) {
                            $data = [
                                'tag' => $token->name,
                                'info' => $err,
                            ];
                            $errors[] = new Error(Error::TAG_INVALID, $token->line, $data);
                        }
                    } else {
                        $data = [
                            'tag' => $token->name,
                        ];
                        $errors[] = new Error(Error::TAG_UNKNOWN, $token->line, $data);
                    }
                    break;
            }
        }
        if (!empty($block)) {
            $blocks[] = self::wrapBlock($block, $options);
        }
        return $blocks;
    }

    /**
     * @param array $block
     * @param array $options
     * @return string
     */
    private static function wrapBlock(array $block, $options)
    {
        $block = \implode('', $block);
        if ($options['bHandler']) {
            return Callback::call($options['bHandler'], $block);
        }
        $t = $options['bTags'];
        return $t[0].$block.$t[1];
    }
}
