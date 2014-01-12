<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Parser;
use axy\ml\helpers\Token;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers axy\ml\Parser::__construct
     * @covers axy\ml\Parser::parse
     * @covers axy\ml\Result::$tokens
     * @covers axy\ml\Result::$meta
     * @covers axy\ml\Result::$title
     * @covers axy\ml\Result::$isCutted
     */
    public function testParse()
    {
        $content = [
            '#  Title of document ',
            '',
            '#= var: value',
            '',
            'Image: [IMG /i/x.png alt]',
            '##[cut] Subtitle',
            'Full text',
        ];
        $content = \implode("\n", $content);
        $parser = new Parser();
        $result = $parser->parse($content, 'cut');
        $this->assertInstanceOf('axy\ml\Result', $result);
        $this->assertInstanceOf('axy\ml\Meta', $result->meta);
        $this->assertSame('value', $result->meta->var);
        $this->assertSame('Title of document', $result->title);
        $this->assertTrue($result->isCutted);
        $expected = [
            [
                'type' => Token::TYPE_HEADER,
                'line' => 1,
                'name' => null,
                'content' => 'Title of document',
                'level' => 1,
            ],
            [
                'type' => Token::TYPE_BLOCK,
                'line' => 5,
                'subs' => [
                    [
                        'type' => Token::TYPE_TEXT,
                        'line' => 5,
                        'content' => 'Image: ',
                    ],
                    [
                        'type' => Token::TYPE_TAG,
                        'line' => 5,
                        'name' => 'img',
                        'content' => ' /i/x.png alt',
                    ],
                ],
            ],
        ];
        $tokens = [];
        foreach ($result->tokens as $token) {
            $tokens[] = $token->asArray();
        }
        $this->assertEquals($expected, $tokens);
    }
}
