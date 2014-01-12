<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

use axy\callbacks\Callback;

/**
 * Tag [Img]
 *
 * @example [IMG /i/a.png]
 * @example [URL /i/a.png Alt text]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Img extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (empty($this->src)) {
            return '';
        }
        return '<img src="'.$this->escape($this->src).'" alt="'.$this->escape($this->alt).'" />';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return $this->alt;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->src = $this->getNextComponent();
        $this->alt = $this->getLastComponent();
        if ($this->options['handler']) {
            $this->src = Callback::call($this->options['handler'], [$this->src]);
        }
        if (empty($this->src)) {
            $this->errors[] = 'empty src';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'handler' => null,
    ];

    /**
     * @var string
     */
    protected $src;

    /**
     * @var string
     */
    protected $alt;
}
