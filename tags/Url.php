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
        if ($params->html !== null) {
            return $params->html;
        }
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
        return $this->params->plain;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->params = (object)[
            'type' => \strtolower($this->getArg()),
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
        if (empty($this->params->url)) {
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
        if ($this->options['css_img']) {
            $this->params->css = $this->options['css_img'];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'css' => null,
        'css_img' => null,
        'handler' => null,
    ];

    /**
     * @var object
     */
    protected $params;
}
