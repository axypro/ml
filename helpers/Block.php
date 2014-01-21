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
     * The flag that the block should be splitted in the current place
     *
     * @var boolean
     */
    public $split;

    /**
     * The flag that the block should be wrapped
     *
     * @var boolean
     */
    public $wrap;

    /**
     * The list of resulting blocks
     *
     * @var array
     */
    public $blocks;

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
     */
    public function render()
    {
        $this->split = false;
        $this->wrap = true;
        $this->blocks = [];
        $context = $this->context;
        $context->setCurrentBlock($this);
        $options = $context->options->getSource();
        $content = &$this->content;
        $content = '';
        $tags = $context->tags;
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
                            if ($this->split) {
                                foreach (\array_reverse($lists) as $l) {
                                    $content .= '</li>'.$lnl.'</'.$l.'>'.$lnl;
                                }
                                $listlevel = 0;
                                $lists = [];
                                $this->addBlock($content, $options, $crblock);
                                $this->addBlock($html, $options, $tag->shouldCreateBlock());
                                $content = '';
                                $this->split = false;
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
            $this->addBlock($content, $options, $crblock);
        }
        $context->setCurrentBlock(null);
    }

    /**
     * @param string $content
     * @param array $options
     * @param boolean $crblock
     * @return array
     */
    private function addBlock($content, $options, $crblock)
    {
        $content = \trim($content);
        if ($content === '') {
            return;
        }
        if ($crblock && $this->wrap) {
            if ($options['bHandler']) {
                $content = Callback::call($options['bHandler'], [$content]);
            } else {
                $t = $options['bTags'];
                $content = $t[0].$content.$t[1];
            }
        }
        $this->blocks[] = $content;
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
