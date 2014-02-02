<?php
/**
 * @package axy/ml
 */

namespace axy\ml\tests;

use axy\ml\Parser;

class ParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Custom brackets
     */
    public function testBracket()
    {
        $content = 'This is {B}bo[B]ld{{{/B}}}';
        $parser1 = new Parser();
        $expected1 = '<p>This is {B}bo<b>ld{{{/B}}}</p>';
        $this->assertSame($expected1, $parser1->parse($content)->html);
        $options2 = [
            'brackets' => ['{', '}'],
        ];
        $parser2 = new Parser($options2);
        $expected2 = '<p>This is <b>bo[B]ld</b></p>';
        $this->assertSame($expected2, $parser2->parse($content)->html);
    }

    /**
     * Not 1-symbol brackets
     */
    public function testBracketLong()
    {
        $options = [
            'brackets' => ['<!--', '-->'],
        ];
        $parser = new Parser($options);
        $content = '<!--B--> and <!--<!--<!--I -->-->-->-->';
        $expected = '<p><b> and <i>--&gt;</p>';
        $this->assertSame($expected, $parser->parse($content)->html);
    }
}
