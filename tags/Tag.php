<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [TAG:htmltag]
 *
 * @example [tag:div style="color:red"]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
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
        return '<'.\strtolower($tag).($this->value ? ' '.$this->value : '').'>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return '';
    }
}
