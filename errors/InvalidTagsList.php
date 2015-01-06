<?php
/**
 * @package axy\ml
 */

namespace axy\ml\errors;

use axy\errors\InvalidConfig;

/**
 * A tags list has an invalid format
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class InvalidTagsList extends InvalidConfig implements Error
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'AxyML tags list has an invalid format: "{{ errorMessage }}"';

    /**
     * Constructor
     *
     * @param string $errorMessage [optional]
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($errorMessage = null, \Exception $previous = null, $thrower = null)
    {
        parent::__construct('TagsList', $errorMessage, 0, $previous, $thrower);
    }
}
