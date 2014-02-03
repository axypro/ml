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
 * @property-read string $nl
 * @property-read int $tab
 * @property-read boolean $escape
 * @property-read callable $textHandler
 * @property-read int $hStart
 * @property-read callable $hHandler
 * @property-read array $bTags
 * @property-read callable $bHandler
 * @property-read boolean $beauty
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
    protected $rigidly = true;
}
