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
        $content = Normalizer::toParse($this->getTokensContent('base'), new Options());
        $data = $this->getTokensData('base');
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize();
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        $this->assertEquals($data['tokens'], $this->tokens2array($tokens));
        $this->assertFalse($tokenizer->isCutted());
    }

    /**
     * @covers ::tokenize
     */
    public function testTokenizeCut()
    {
        $content = Normalizer::toParse($this->getTokensContent('base'), new Options());
        $data = $this->getTokensData('base');
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize('cut');
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        $expected = \array_slice($data['tokens'], 0, 4);
        $this->assertEquals($expected, $this->tokens2array($tokens));
        $this->assertTrue($tokenizer->isCutted());
    }

    /**
     * @covers ::tokenize
     */
    public function testTokenizeList()
    {
        $content = Normalizer::toParse($this->getTokensContent('list'), new Options());
        $data = $this->getTokensData('list');
        $tokenizer = new Tokenizer($content);
        $tokenizer->tokenize();
        $tokens = $tokenizer->getTokens();
        $this->assertEquals($data['tokens'], $this->tokens2array($tokens));
    }

    /**
     * @covers ::tokenize
     */
    public function testTokenizeH()
    {
        $noptions = [
            'hLinkPrefix' => 'p-',
            'hLinkNeed' => true,
        ];
        $options = new Options($noptions);
        $content = Normalizer::toParse($this->getTokensContent('base'), $options);
        $data = $this->getTokensData('base-h');
        $tokenizer = new Tokenizer($content, $options);
        $tokenizer->tokenize();
        $this->assertEquals($data['meta'], $tokenizer->getMeta()->getSource());
        $tokens = $tokenizer->getTokens();
        $this->assertEquals($data['tokens'], $this->tokens2array($tokens));
        $this->assertFalse($tokenizer->isCutted());
    }

    /**
     * @covers ::getDuration
     */
    public function testGetDuration()
    {
        $tokenizer = new Tokenizer('content');
        $this->assertNull($tokenizer->getDuration());
        $tokenizer->tokenize();
        $this->assertInternalType('float', $tokenizer->getDuration());
    }

    /**
     * @covers ::replaceTokens
     */
    public function testReplaceTokens()
    {
        $tokenizer = new Tokenizer('content');
        $tokenizer->tokenize();
        $tokens = [
            [
                'type' => 'header',
                'line' => 2,
                'name' => null,
                'content' => 'This is header',
                'level' => 1,
                'link' => 'p-h-1',
            ],
        ];
        $this->assertNotEquals($tokens, $tokenizer->getTokens());
        $tokenizer->replaceTokens($tokens);
        $this->assertEquals($tokens, $tokenizer->getTokens());
    }

    /**
     * @param string $name
     * @return string
     */
    private function getTokensContent($name)
    {
        return \file_get_contents(__DIR__.'/../nstst/tokens/'.$name.'.txt');
    }

    /**
     * @param string $name
     * @return array
     */
    private function getTokensData($name)
    {
        return include(__DIR__.'/../nstst/tokens/'.$name.'.php');
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
