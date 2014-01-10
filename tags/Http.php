<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [HTTP]
 *
 * @example [http://example.loc/]
 * @example [http://example.loc/ Example link]
 * @example [http"://link with space" Caption]
 *
 * @author Oleg Grigoriev
 */
class Http extends LinkBase
{
    /**
     * {@inheritdoc}
     */
    protected $protocol = 'http';
}
