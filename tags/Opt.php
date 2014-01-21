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
            foreach ($args as $arg) {
                switch (\strtolower($arg)) {
                    case 'nop':
                        $this->context->block->wrap = false;
                        break;
                    default:
                        $this->errors[] = 'unknown "'.$arg.'"';
                }
            }
        }
        return '';
    }
}
