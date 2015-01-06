<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\helpers\Token;

/**
 * coversDefaultClass axy\ml\helpers\Token
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::__construct
     */
    public function testCreate()
    {
        $token = new Token('header', 20);
        $token->name = 'this is name';
        $token->any = 'Any!';
        $this->assertSame('header', $token->type);
        $this->assertSame(20, $token->line);
        $this->assertSame('this is name', $token->name);
        $this->assertSame('Any!', $token->any);
    }

    /**
     * covers ::append
     * covers ::getSubs
     */
    public function testAppend()
    {
        $token = new Token('block');
        $token1 = new Token('text');
        $token2 = new Token('tag');
        $this->assertEmpty($token->getSubs());
        $token->append($token1);
        $token->append($token2);
        $this->assertEquals([$token1, $token2], $token->getSubs());
    }

    /**
     * covers ::asArray
     */
    public function testAsArray()
    {
        $token = new Token('block', 10);
        $token1 = new Token('text', 11);
        $token2 = new Token('tag');
        $token2->any = 'Any';
        $token->append($token1);
        $token->append($token2);
        $expected = [
            'type' => 'block',
            'line' => 10,
            'subs' => [
                [
                    'type' => 'text',
                    'line' => 11,
                ],
                [
                    'type' => 'tag',
                    'line' => null,
                    'any' => 'Any',
                ],
            ],
        ];
        $this->assertEquals($expected, $token->asArray());
    }

    /**
     * covers ::__toString
     */
    public function testToString()
    {
        $token = new Token('block', 12);
        $this->assertSame('[block token:12]', ''.$token);
    }
}
