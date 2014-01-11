<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Class for closing tags like a [/B]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ClosingTag extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (!$this->tagname) {
            return '';
        }
        return '</'.$this->tagname.($this->tagattrs ? ' '.$this->tagattrs : '').'>';
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
        $this->tagname = \strtolower($this->getNextComponent());
        $this->tagattrs = $this->getLastComponent();
        if (!$this->tagname) {
            $this->errors[] = 'empty closing tag';
        }
    }

    /**
     * @var string
     */
    private $tagname;

    /**
     * @var string
     */
    private $tagattrs;
}
