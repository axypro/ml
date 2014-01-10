<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [HTTPS]
 *
 * @example [https://example.loc/]
 * @example [https://example.loc/ Example link]
 * @example [https"://link with space" Caption]
 *
 * @author Oleg Grigoriev
 */
class Https extends LinkBase
{
    /**
     * {@inheritdoc}
     */
    protected $protocol = 'https';
}
