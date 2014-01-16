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
        if (isset($token->name) && ($token->name !== '')) {
            $attr = ' id="'.self::escape($token->name).'"';
        } else {
            $attr = '';
        }
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
        $lastCr = true;
        $listlevel = 0;
        $list = null;
        $lists = [];
        foreach ($container->subs as $token) {
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $lastCr = true;
                    $block[] = self::text($token->content, $options);
                    break;
                case Token::TYPE_TAG:
                    $tag = $tags->create($token->name, $token->content);
                    if ($tag) {
                        $html = $tag->getHtml();
                        foreach ($tag->getErrors() as $err) {
                            $data = [
                                'tag' => $token->name,
                                'info' => $err,
                            ];
                            $errors[] = new Error(Error::TAG_INVALID, $token->line, $data);
                        }
                        if (empty($block)) {
                            $block[] = $html;
                        } else {
                            if ($tag->shouldSplitBlock()) {
                                foreach (\array_reverse($lists) as $l) {
                                    $block[] = '</li></'.$l.'>';
                                }
                                $listlevel = 0;
                                $lists = [];
                                $blocks[] = self::wrapBlock($block, $options, $lastCr);
                                $blocks[] = self::wrapBlock([$html], $options, $tag->shouldCreateBlock());
                                $block = [];
                            } else {
                                $block[] = $html;
                            }
                        }
                        $lastCr = $tag->shouldCreateBlock();
                    } else {
                        $data = [
                            'tag' => $token->name,
                        ];
                        $errors[] = new Error(Error::TAG_UNKNOWN, $token->line, $data);
                    }
                    break;
                case Token::TYPE_LI:
                    $delta = $token->level - $listlevel;
                    if ($delta > 0) {
                        $list = ($token->start === null) ? 'ul' : 'ol';
                        for ($i = 0; $i < $delta; $i++) {
                            $s = ($token->start > 1) ? ' start="'.$token->start.'"' : '';
                            $block[] = '<'.$list.$s.'><li>';
                            $lists[] = $list;
                        }
                    } elseif ($delta < 0) {
                        $rlists = \array_reverse(\array_slice($lists, $token->level));
                        $lists = \array_slice($lists, 0, $token->level);
                        foreach ($rlists as $l) {
                            $block[] = '</li></'.$l.'>';
                        }
                        $block[] = '</li><li>';
                    } else {
                        $block[] = '</li><li>';
                    }
                    $listlevel = $token->level;
                    break;
            }
        }
        if (!empty($block)) {
            foreach (\array_reverse($lists) as $l) {
                $block[] = '</li></'.$l.'>';
            }
            $blocks[] = self::wrapBlock($block, $options, $lastCr);
        }
        return $blocks;
    }

    /**
     * @param array $block
     * @param array $options
     * @param boolean $lastCr
     * @return string
     */
    private static function wrapBlock(array $block, $options, $lastCr)
    {
        if ((!$lastCr) && (\count($block) === 1)) {
            return $block[0];
        }
        $block = \trim(\implode('', $block));
        if ($options['bHandler']) {
            return Callback::call($options['bHandler'], [$block]);
        }
        $t = $options['bTags'];
        return $t[0].$block.$t[1];
    }
}
