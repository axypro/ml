<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst\tags;

class Two extends \axy\ml\tags\Base
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
