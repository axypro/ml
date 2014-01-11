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
 * @author Oleg Grigoriev
 */
class Url extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (empty($this->url)) {
            return '';
        }
        $caption = $this->caption ?: $this->url;
        return '<a href="'.$this->escape($this->url).'">'.$this->escape($caption).'</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        if (empty($this->url)) {
            return '';
        }
        if ($this->caption) {
            return $this->url.' '.$this->caption;
        }
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->url = $this->getNextComponent();
        $this->caption = $this->getLastComponent();
        if ($this->options['handler']) {
            $this->url = Callback::call($this->options['handler'], [$this->url]);
        }
        if (empty($this->url)) {
            $this->errors[] = 'empty url';
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
    protected $url;

    /**
     * @var string
     */
    protected $caption;
}
