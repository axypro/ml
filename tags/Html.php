<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [HTML]
 *
 * @example [html <script>alert(1);</script>]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Html extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        return \trim($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return \strip_tags($this->getHTML());
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;

    /**
     * {@inheritdoc}
     */
    protected $createBlock = false;
}
