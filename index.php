<?php
/**
 * Simple markup language
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/ml/master/LICENSE MIT
 * @link https://github.com/axypro/ml repository
 * @uses PHP5.4+
 */

namespace axy\ml;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
