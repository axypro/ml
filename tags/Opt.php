<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

/**
 * Tag [Opt]
 *
 * @example [Opt:nop]
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
                $arg = strtolower($arg);
                $block->opts[$arg] = true;
            }
        }
        return '';
    }
}
