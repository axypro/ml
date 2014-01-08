<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

/**
 * The meta data dictionary
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Meta extends \axy\magic\ArrayWrapper
{
    use \axy\magic\Named;

    /**
     * Constructor
     *
     * @param array $data [optional]
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data, false, false);
    }

    /**
     * Get a data value by a name
     *
     * @param string $name
     * @param mixed $default [optional]
     * @returm mixed
     */
    public function value($name, $default = null)
    {
        if (!$this->exists($name)) {
            return $default;
        }
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    protected $magicName = 'AxyML-Meta';
}
