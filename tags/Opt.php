<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [Opt]
 *
 * @example [Opt:nop]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Opt extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $args = $this->args;
        if (empty($args)) {
            $this->errors[] = 'empty arguments list';
        } else {
            $block = $this->context->block;
            foreach ($args as $arg) {
                $arg = \strtolower($arg);
                $block->opts[$arg] = true;
            }
        }
        return '';
    }
}
