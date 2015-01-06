<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

/**
 * A simple profiler for the parsing
 */
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
