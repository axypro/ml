<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\helpers;

use axy\ml\Context;
use axy\ml\Error;
use axy\callbacks\Callback;

/**
 * Parameters of a current block (during render)
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
     * The flag that the current single tag must be wrapped to paragraph
     *
     * @var boolean
     */
    public $create;

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
     * The list of listeners of finish block render
     *
     * @var array
     */
    public $endListeners;

    /**
     * The flag that the current text must be trimmed
     *
     * @var boolean
     */
    public $ltrim;

    /**
     * Options (changes by [OPT])
     *
     * @var array
     */
    public $opts = [];

    /**
     * The construct
     *
     * @param \axy\ml\helpers\Token $container
     *        a token of the current block
     * @param Context $context
     *        the parsing context
     */
    public function __construct(Token $container, Context $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    /**
     * Renders the block
     */
    public function render()
    {
        $this->split = false;
        $this->create = true;
        $this->wrap = true;
        $this->opts = [];
        $this->endListeners = [];
        $this->ltrim = false;
        $this->blocks = [];
        $context = $this->context;
        $context->setCurrentBlock($this);
        $options = $context->options->getSource();
        $content = &$this->content;
        $content = '';
        $tags = $context->tags;
        $first = null;
        $lastCreate = true;
        foreach ($this->container->subs as $token) {
            $first = ($first === null) ? true : false;
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $text = Highlight::text($token->content, $options);
                    if ($this->ltrim) {
                        $text = ltrim($text);
                        $this->ltrim = false;
                    }
                    $content .= $text;
                    break;
                case Token::TYPE_TAG:
                    $this->ltrim = false;
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
                        $lastCreate = $first ? $this->create : true;
                        if ($content === '') {
                            $content = $html;
                        } else {
                            if ($this->split) {
                                $this->addBlock($options, $lastCreate);
                                $content = $html;
                                $this->addBlock($options, $this->create);
                                $lastCreate = true;
                                $first = null;
                                $content = '';
                                $this->split = false;
                                $this->wrap = true;
                                $this->opts = [];
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
                case Token::TYPE_HTML:
                    $content .= $token->content;
            }
        }
        $this->addBlock($options, $lastCreate);
        $context->setCurrentBlock(null);
    }

    /**
     * @param array $options
     * @param boolean $crBlock
     * @return array
     */
    private function addBlock($options, $crBlock)
    {
        if (!empty($this->endListeners)) {
            foreach ($this->endListeners as $listener) {
                call_user_func($listener, $this);
            }
            $this->endListeners = [];
        }
        $content = trim($this->content);
        if ($content === '') {
            return;
        }
        if ($crBlock && $this->wrap) {
            if ($options['bHandler']) {
                $content = Callback::call($options['bHandler'], [$content, $this->opts]);
            } elseif (empty($this->opts['nop'])) {
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
     * @var Context
     */
    private $context;
}
