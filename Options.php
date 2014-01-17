<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Config;

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
 * @property-read callable $hHandler
 * @property-read array $bTags
 * @property-read callable $bHandler
 * @property-read string $blocks_separator
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
