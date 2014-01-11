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
 * @author Oleg Grigoriev
 */
class Scheme extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (empty($this->url)) {
            return '';
        }
        $url = $this->name.$this->url;
        $content = $this->content ?: $url;
        return '<a href="'.$this->escape($url).'">'.$this->escape($content).'</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        if (empty($this->url)) {
            return '';
        }
        $url = $this->name.$this->url;
        return $url.($this->content ? ' '.$this->content : '');
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        $this->url = $this->getNextComponent();
        $this->content = $this->getLastComponent();
        if (empty($this->url)) {
            $this->errors[] = 'empty url';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $content;
}
