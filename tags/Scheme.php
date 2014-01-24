<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tags [HTTP] & [HTTPS]
 *
 * @example [http://example.loc/]
 * @example [http://example.loc/ Example link]
 * @example [http"://link with space" Caption]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Scheme extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if ($this->urltag) {
            return $this->urltag->getHTML();
        } else {
            $url = $this->escape($this->url);
            $css = $this->options['css'];
            if ($css !== null) {
                $css = ' class="'.$this->escape($css).'"';
            }
            return '<a href="'.$url.'"'.$css.'>'.$url.'</a>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        if ($this->urltag) {
            return $this->urltag->getPlain();
        } else {
            return $this->url;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        $this->url = $this->name.$this->getNextComponent();
        $content = $this->getLastComponent();
        if ($this->options['use_url']) {
            $content = ':plain "'.$this->url.'" '.$content;
            $this->urltag = $this->context->tags->create('url', $content, $this->context);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'use_url' => true,
        'css' => null,
    ];

    /**
     * @var string
     */
    private $url;

    /**
     * @var \axy\ml\tags\Url
     */
    private $urltag;
}
