<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst;

use axy\ml\Context;
use axy\ml\Result;
use axy\ml\Options;
use axy\ml\TagsList;
use axy\ml\helpers\Tokenizer;
use axy\ml\helpers\Token;
use axy\ml\helpers\Block;

class Factory
{
    /**
     * Create an instance of context for testing
     *
     * @param array $options [optional]
     * @param array $tags [optional]
     * @param mixed $custom
     * @return \axy\ml\Context
     */
    public static function createContext($options = null, $tags = null, $custom = null, $withblock = true)
    {
        $tokenizer = new Tokenizer('');
        $options = new Options($options);
        $tags = new TagsList($tags);
        $result = new Result($tokenizer, $options, $tags, $custom);
        $context = new Context($result, $options, $tags, $custom);
        if ($withblock) {
            $container = new Token(Token::TYPE_BLOCK, 1);
            $container->subs = [];
            $block = new Block($container, $context);
            $block->content = '';
            $block->split = false;
            $block->create = true;
            $context->setCurrentBlock($block);
        }
        return $context;
    }
}
