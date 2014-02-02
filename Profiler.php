<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

class Profiler extends \stdClass
{
    /**
     * The time of tokenize
     *
     * @var float
     */
    public $tokenize;

    /**
     * The time of html highlight
     *
     * @var float
     */
    public $html;

    /**
     * The time of plain represent
     *
     * @var float
     */
    public $plain;
}