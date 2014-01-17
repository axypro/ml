<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst\tags;

class Cus extends \axy\ml\tags\Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $custom = $this->context->custom;
        $x = isset($custom['value']) ? $custom['value'] : null;
        $y = $this->getArg(null, 2);
        return $x.'+'.$y.'='.($x + $y);
    }
}
