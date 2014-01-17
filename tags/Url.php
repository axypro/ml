<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

use axy\callbacks\Callback;

/**
 * Tag [URL]
 *
 * @example [URL http://example.loc/]
 * @example [URL http://example.loc/ Example link]
 * @example [URL "http://link with space" Caption]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Url extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $params = $this->params;
        if (empty($params->url)) {
            return '';
        }
        $url = $this->escape($params->url);
        $css = $params->css ? ' class="'.$this->escape($params->css).'"' : '';
        return '<a href="'.$url.'"'.$css.'>'.$params->caption.'</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        $params = $this->params;
        if (empty($params->url)) {
            return '';
        }
        if ($params->plain) {
            return $params->url.' '.$params->plain;
        }
        return $params->url;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $url = $this->getNextComponent();
        $caption = $this->getLastComponent();
        $this->params = (object)[
            'url' => $url,
            'caption' => $this->escape($caption ?: $url),
            'plain' => $caption,
            'css' => $this->options['css'],
        ];
        if ($this->options['handler']) {
            $this->url = Callback::call($this->options['handler'], [$this->params]);
        }
        if (empty($this->params->url)) {
            $this->errors[] = 'empty url';
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
     * @var array
     */
    protected $params;
}
