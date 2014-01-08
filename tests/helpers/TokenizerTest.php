<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\helpers\Tokenizer;
use axy\ml\helpers\Normalizer;
use axy\ml\Options;

/**
 * @coversDefaultClass axy\ml\helpers\Tokenizer
 */
class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::tokenize
     */
    public function testTokenize()
    {
        $dir = __DIR__.'/../nstst/';
        $content = \file_get_contents($dir.'tokens.txt');
        $data = include($dir.'tokens.php');
        $content = Normalizer::toParse($content, new Options());
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize();
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        foreach ($tokens as &$item) {
            $item = $item->asArray();
        }
        unset($item);
        $this->assertEquals($data['tokens'], $tokens);
    }
}
