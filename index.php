<?php
/**
 * Simple markup language
 *
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/ml/master/LICENSE MIT
 * @link https://github.com/axypro/ml repository
 * @link https://github.com/axypro/ml/wiki documentation
 * @uses PHP5.4+
 */

namespace axy\ml;

if (!\is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: ./composer.phar install --dev');
}

require_once(__DIR__.'/vendor/autoload.php');
