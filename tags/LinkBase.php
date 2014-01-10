<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * The basic class for tag [http] and [https]
 *
 * @author Oleg Grigoriev
 */
class LinkBase extends Base
{
    /**
     * Protocol http or https
     * (for override)
     *
     * @var string
     */
    protected $protocol;

    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (empty($this->url)) {
            return '';
        }
        $url = $this->protocol.$this->url;
        $caption = $this->caption ?: $url;
        return '<a href="'.$this->escape($url).'">'.$this->escape($caption).'</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        if (empty($this->url)) {
            return '';
        }
        $url = $this->protocol.$this->url;
        if (empty($this->caption)) {
            return $url;
        }
        return $url.' '.$this->caption;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->url = $this->getNextComponent();
        $this->caption = $this->getLastComponent();
        if (empty($this->url)) {
            $this->errors[] = 'empty link';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $caption;
}
