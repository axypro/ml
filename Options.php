<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Config;

/**
 * The options list of a parser
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read array $brackets
 *                the tag brackets
 * @property-read string $nl
 *                the new-line symbol(s) in result
 * @property-read int $tab
 *                the count of spaces for a tab
 * @property-read boolean $escape
 *                the flag that escape html special chars
 * @property-read callable $textHandler
 *                the handler for text
 * @property-read int $hStart
 *                number of a top level header (<h1> by default)
 * @property-read callable $hHandler
 *                the wrapper for header (callable)
 * @property-read string $hLinkPrefix
 *                the prefix for a header link
 * @property-read boolean $hLinkNeed
 *                the flag that a header link is necessary
 * @property-read array $bTags
 *                the opening and closing html-tags for text blocks
 * @property-read callable $bHandler
 *                the wrapper for a block
 * @property-read boolean $beauty
 *                the beautiful output flag
 */
class Options extends \axy\magic\ArrayWrapper
{
    use \axy\magic\Named;

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
