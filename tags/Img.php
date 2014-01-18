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
        if ($params->html !== null) {
            return $params->html;
        }
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
        return $this->params->plain;
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
            'html' => null,
        ];
        $this->params->plain = $this->params->alt;
        if ($this->options['handler']) {
            Callback::call($this->options['handler'], [$this->params]);
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
