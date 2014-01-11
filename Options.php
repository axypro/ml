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
        $this->source = self::loadDefaultOptions();
        parent::__construct($options, true, true);
    }

    /**
     * @return array
     */
    private static function loadDefaultOptions()
    {
        static $options;
        if (!$options) {
            $options = include(__DIR__.'/config/options.php');
        }
        return $options;
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
