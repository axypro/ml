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
        $content = Normalizer::toParse($this->getTokensContent(), new Options());
        $data = $this->getTokensData();
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize();
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        $this->assertEquals($data['tokens'], $this->tokens2array($tokens));
    }

    /**
     * @covers ::tokenize
     */
    public function testTokenizeCut()
    {
        $content = Normalizer::toParse($this->getTokensContent(), new Options());
        $data = $this->getTokensData();
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize('cut');
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        $expected = \array_slice($data['tokens'], 0, 4);
        $this->assertEquals($expected, $this->tokens2array($tokens));
    }

    /**
     * @return string
     */
    private function getTokensContent()
    {
        static $content;
        if ($content === null) {
            $content = \file_get_contents(__DIR__.'/../nstst/tokens.txt');
        }
        return $content;
    }

    /**
     * @return array
     */
    private function getTokensData()
    {
        static $data;
        if ($data === null) {
            $data = include(__DIR__.'/../nstst/tokens.php');
        }
        return $data;
    }

    /**
     * @param array $tokens
     * @return array
     */
    private function tokens2array(array $tokens)
    {
        foreach ($tokens as &$item) {
            $item = $item->asArray();
        }
        unset($item);
        return $tokens;
    }
}
