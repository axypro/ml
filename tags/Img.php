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
        $params = $this->params;
        if (empty($params->src)) {
            return '';
        }
        $attrs = [
            'src="'.$this->escape($params->src).'"',
            'alt="'.$this->escape($params->alt).'"',
        ];
        if ($params->css) {
            $attrs[] = 'class="'.$this->escape($params->css).'"';
        }
        return '<img '.(\implode(' ', $attrs)).' />';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return $this->params->alt;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->params = (object)[
            'src' => $this->getNextComponent(),
            'alt' => $this->getLastComponent(),
            'css' => $this->options['css'],
            'context' => $this->context,
        ];
        if ($this->options['handler']) {
            $this->params->src = Callback::call($this->options['handler'], [$this->params->src]);
        }
        if (empty($this->params->src)) {
            $this->errors[] = 'empty src';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'css' => null,
        'handler' => null,
    ];

    /**
     * @var object
     */
    protected $params;
}
