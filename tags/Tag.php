<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

/**
 * Tag [TAG:htmltag]
 *
 * @example [tag:div style="color:red"]
 */
class Tag extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $tag = $this->getArg();
        if ($tag === null) {
            $this->errors = ['empty html tag'];
            return '';
        }
        return '<'.strtolower($tag).($this->value ? ' '.$this->value : '').'>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return '';
    }
}
