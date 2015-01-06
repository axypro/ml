<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\ml\helpers\Config;
use axy\magic\ArrayWrapper;
use axy\magic\Named;

/**
 * The parser options list
 *
 * @property-read array $brackets
 *                the tag brackets
 * @property-read string $nl
 *                the new-line symbol(s) for the result
 * @property-read int $tab
 *                the count of spaces for a tab
 * @property-read boolean $escape
 *                the flag that escape html special chars
 * @property-read callable $textHandler
 *                the handler for plain text
 * @property-read int $hStart
 *                the number of a top level header (<h1> by default)
 * @property-read callable $hHandler
 *                the wrapper for a header (callable)
 * @property-read string $hLinkPrefix
 *                the prefix for a header link
 * @property-read boolean $hLinkNeed
 *                the flag that a header link is necessary
 * @property-read array $bTags
 *                the opening and the closing html-tags for text blocks
 * @property-read callable $bHandler
 *                the wrapper for a block
 * @property-read boolean $beauty
 *                the beautiful output flag
 */
class Options extends ArrayWrapper
{
    use Named;

    /**
     * Constructor
     *
     * @param array $options [optional]
     */
    public function __construct(array $options = null)
    {
        $this->source = Config::getOptions();
        parent::__construct($options, true, true);
    }

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'AxyML-Options';

    /**
     * {@inheritdoc}
     */
    protected $fixed = true;
}
