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
     * The list of listeners of finish block render
     *
     * @var array
     */
    public $endListeners;

    public $ltrim;

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
        $this->endListeners = [];
        $this->ltrim = false;
        $this->blocks = [];
        $context = $this->context;
        $context->setCurrentBlock($this);
        $options = $context->options->getSource();
        $content = &$this->content;
        $content = '';
        $tags = $context->tags;
        foreach ($this->container->subs as $token) {
            $crblock = true;
            switch ($token->type) {
                case Token::TYPE_TEXT:
                    $text = Highlight::text($token->content, $options);
                    if ($this->ltrim) {
                        $text = \ltrim($text);
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
                        if ($content === '') {
                            $content = $html;
                            if (!$tag->shouldCreateBlock()) {
                                $crblock = false;
                            }
                        } else {
                            if ($this->split) {
                                $this->addBlock($options, $crblock);
                                $content = $html;
                                $this->addBlock($options, $tag->shouldCreateBlock());
                                $content = '';
                                $this->split = false;
                                $this->wrap = true;
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
            }
        }
        $this->addBlock($options, $crblock);
        $context->setCurrentBlock(null);
    }

    /**
     * @param array $options
     * @param boolean $crblock
     * @return array
     */
    private function addBlock($options, $crblock)
    {
        if (!empty($this->endListeners)) {
            foreach ($this->endListeners as $listener) {
                \call_user_func($listener, $this);
            }
            $this->endListeners = [];
        }
        $content = \trim($this->content);
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
