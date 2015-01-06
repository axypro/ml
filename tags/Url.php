<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

use axy\callbacks\Callback;

/**
 * Tag [URL]
 *
 * @example [URL http://example.loc/]
 * @example [URL http://example.loc/ Example link]
 * @example [URL "http://link with space" Caption]
 */
class Url extends Base
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
        if ($params->url === '') {
            return '';
        }
        $url = $this->escape($params->url);
        $css = $params->css;
        if ($css !== null) {
            $css = ' class="'.$this->escape($params->css).'"';
        }
        if ($params->type === 'code') {
            if ($this->options['css_code'] !== null) {
                $codeCss = ' class="'.$this->escape($this->options['css_code']).'"';
            } else {
                $codeCss = '';
            }
            $caption = '<code'.$codeCss.'>'.$params->caption.'</code>';
        } else {
            $caption = $params->caption;
        }
        return '<a href="'.$url.'"'.$css.'>'.$caption.'</a>';
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
            'type' => strtolower($this->getArg()),
            'url' => $this->getNextComponent(),
            'caption' => null,
            'context' => $this->context,
            'plain' => null,
            'html' => null,
            'css' => $this->options['css'],
        ];
        $params = $this->params;
        $params->plain = $params->url;
        if ($params->type === 'img' && ($this->value !== '')) {
            $this->loadImg();
        } else {
            $params->caption = $this->getLastComponent();
            if ($params->caption !== '') {
                $params->plain .= ' '.$params->caption;
            } else {
                $params->caption = $params->url;
            }
            if ($params->type !== 'html') {
                $params->caption = $this->escape($params->caption);
            }
        }
        if ($this->options['handler']) {
            Callback::call($this->options['handler'], [$this->params]);
        }
        if ($this->params->url === '') {
            $this->errors[] = 'empty url';
        }
    }

    protected function loadImg()
    {
        $tag = $this->context->tags->create('img', $this->value, $this->context);
        if (!$tag) {
            return;
        }
        $this->params->caption = $tag->getHTML();
        $plain = $tag->getPlain();
        if ($plain) {
            $this->params->plain .= ' '.$plain;
        }
        if ($this->options['css_img'] !== null) {
            $this->params->css = $this->options['css_img'];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'css' => null,
        'css_img' => null,
        'css_code' => null,
        'handler' => null,
    ];

    /**
     * @var object
     */
    protected $params;
}
