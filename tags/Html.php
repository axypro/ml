<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

/**
 * Tag [HTML]
 *
 * @example [html <script>alert(1);</script>]
 */
class Html extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $this->context->block->create = false;
        return trim($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return strip_tags($this->getHTML());
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;
}
