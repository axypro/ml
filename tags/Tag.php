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
 * @author Oleg Grigoriev
 */
class Tag extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        if (empty($this->args[0])) {
            $this->errors = ['empty html tag'];
            return '';
        }
        return '<'.\strtolower($this->args[0]).($this->value ? ' '.$this->value : '').'>';
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return '';
    }
}
