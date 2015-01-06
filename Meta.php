<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml;

use axy\magic\ArrayWrapper;
use axy\magic\Named;

/**
 * The meta data dictionary
 */
class Meta extends ArrayWrapper
{
    use Named;

    /**
     * The constructor
     *
     * @param array $data [optional]
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data, false, false);
    }

    /**
     * Returns a data value by name
     *
     * @param string $name
     * @param mixed $default [optional]
     * @return mixed
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
