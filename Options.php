<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

/**
 * Options of parser
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 *
 * @property-read string $nl
 * @property-read int $tab
 * @property-read boolean $escape
 * @property-read callable $texthandler
 * @property-read int $hStart
 * @property-read array $pTags
 * @property-read callable $pHandler
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
        parent::__construct($options, true, true);
    }

    /**
     * Default options
     *
     * @var array
     */
    protected $source = [
        'nl' => "\n", // newline symbol(s) in result
        'tab' => 4, // count of spaces for tab
        'escape' => true, // escape html special chars
        'textHandler' => null, // handler for text (callback)
        'hStart' => 1, // number of top level <h> (<h1> by default)
        'hHandler' => null, // wrapper for header (callback)
        'pTags' => ['<p>', '</p>'], // tags for paragraph
        'pHandler' => null, // wrapper for paragraph (callback)
    ];

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'AxyML-Options';

    /**
     * {@inheritdoc}
     */
    protected $rigidly = true;
}
