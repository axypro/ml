<?php
/**
 * @package axy\ml
 */

namespace axy\ml\helpers;

use axy\ml\Error;
use axy\callbacks\Callback;

/**
 * Parameters of current block (during render)
 *
 * @autor Oleg Grigoriev <go.vasac@gmail.com>
 */
class Block
{
    /**
     * The block content that has already been rendered
     *
     * @var string
     */
    public $content;

    /**
     * Construct
     *
     * @param \axy\ml\helpers\Token $container
     *        a token of the current block
     * @param \axy\ml\Context $context
     *        the parsing context
     */
    public function __construct(Token $container, \axy\ml\Context $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    /**
     * Render block
     *
     * @param array &$errors
     *        a list of parsing errors
     * @return string
     */
    public function getHTMLBlocks()
    {
        $context = $this->context;
        $options = $context->options->getSource();
        $content = &$this->content;
        $content = '';
        $tags = $context->tags;
        $blocks = [];
        $listlevel = 0;
        $list = null;
        $lists = [];
        $lnl = $options['beauty'] ? "\n" : '';
        foreach ($this->container->subs as $token) {
            $crblock = true;
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $content .= Highlight::text($token->content, $options);
                    break;
                case Token::TYPE_TAG:
                    $tag = $tags->create($token->name, $token->content, $context);
                    if ($tag) {
                        $html = $tag->getHtml();
                        foreach ($tag->getErrors() as $err) {
                            $data = [
                                'tag' => $token->name,
                                'info' => $err,
                            ];
                            $context->addError(new Error(Error::TAG_INVALID, $token->line, $data));
                        }
                        if ($content === '') {
                            $content = $html;
                            if (!$tag->shouldCreateBlock()) {
                                $crblock = false;
                            }
                        } else {
                            if ($tag->shouldSplitBlock()) {
                                foreach (\array_reverse($lists) as $l) {
                                    $content .= '</li>'.$lnl.'</'.$l.'>'.$lnl;
                                }
                                $listlevel = 0;
                                $lists = [];
                                $blocks[] = self::wrapBlock($content, $options, $crblock);
                                $blocks[] = self::wrapBlock($html, $options, $tag->shouldCreateBlock());
                                $content = '';
                            } else {
                                $content .= $html;
                            }
                        }
                    } else {
                        $data = [
                            'tag' => $token->name,
                        ];
                        $context->addError(new Error(Error::TAG_UNKNOWN, $token->line, $data));
                    }
                    break;
                case Token::TYPE_LI:
                    $delta = $token->level - $listlevel;
                    if ($delta > 0) {
                        $list = ($token->start === null) ? 'ul' : 'ol';
                        for ($i = 0; $i < $delta; $i++) {
                            $s = ($token->start > 1) ? ' start="'.$token->start.'"' : '';
                            $content .= (($content !== '') ? $lnl : '').'<'.$list.$s.'>'.$lnl.'<li>';
                            $lists[] = $list;
                        }
                    } elseif ($delta < 0) {
                        $rlists = \array_reverse(\array_slice($lists, $token->level));
                        $lists = \array_slice($lists, 0, $token->level);
                        foreach ($rlists as $l) {
                            $content .= '</li>'.$lnl.'</'.$l.'>';
                        }
                        $content .= '</li>'.$lnl.'<li>';
                    } else {
                        $content .= '</li>'.$lnl.'<li>';
                    }
                    $listlevel = $token->level;
                    break;
            }
        }
        if ($content !== '') {
            foreach (\array_reverse($lists) as $l) {
                $content .= '</li>'.$lnl.'</'.$l.'>';
            }
            $blocks[] = self::wrapBlock($content, $options, $crblock);
        }
        return $blocks;
    }

    /**
     * @param array $block
     * @param array $options
     * @param boolean $crblock
     * @return array
     */
    private static function wrapBlock($content, $options, $crblock)
    {
        $content = \trim($content);
        if ((!$crblock) || ($content === '')) {
            return $content;
        }
        if ($options['bHandler']) {
            return Callback::call($options['bHandler'], [$content]);
        }
        $t = $options['bTags'];
        return $t[0].$content.$t[1];
    }

    /**
     * @var \axy\ml\helpers\Token
     */
    private $container;

    /**
     * @var \axy\ml\Context
     */
    private $context;
}
