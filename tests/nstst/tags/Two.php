<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst\tags;

use axy\ml\tags\Base;

class Two extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        return $this->escape($this->getPlain());
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return ($this->options['x'] + $this->options['y']).':'.$this->value;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->value .= '!';
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'x' => 1,
        'y' => 2,
    ];
}
