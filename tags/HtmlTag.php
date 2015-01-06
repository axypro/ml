<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

/**
 * Html tags like [B], [I], [TABLE]
 *
 * @example [http://example.loc/]
 * @example [http://example.loc/ Example link]
 * @example [http"://link with space" Caption]
 */
class HtmlTag extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $content = '';
        if ($this->value) {
            $content .= ' '.$this->value;
        }
        if ($this->options['single']) {
            $content .= ' /';
        }
        return '<'.$this->name.$content.'>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'single' => false,
    ];
}
