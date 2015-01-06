<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

/**
 * Class for closing tags like [/B]
 */
class ClosingTag extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if ($this->tagName === '') {
            return '';
        }
        return '</'.$this->tagName.($this->tagAttrs ? ' '.$this->tagAttrs : '').'>';
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
    protected function parse()
    {
        $this->tagName = strtolower($this->getNextComponent());
        $this->tagAttrs = $this->getLastComponent();
        if ($this->tagName === '') {
            $this->errors[] = 'empty closing tag';
        }
    }

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var string
     */
    private $tagAttrs;
}
