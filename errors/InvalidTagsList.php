<?php
/**
 * @package axy\ml
 */

namespace axy\ml\errors;

/**
 * A tags list has an invalid format
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class InvalidTagsList extends \axy\errors\InvalidConfig implements Error
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'AxyML tags list has an invalid format: "{{ errmsg }}"';

    /**
     * Constructor
     *
     * @param string $errmsg [optional]
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($errmsg = null, \Exception $previous = null, $thrower = null)
    {
        parent::__construct('TagsList', $errmsg, 0, $previous, $thrower);
    }
}
