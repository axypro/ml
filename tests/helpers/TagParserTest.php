<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\helpers;

use axy\ml\helpers\TagParser;

/**
 * @coversDefaultClass axy\ml\helpers\TagParser
 */
class TagParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::loadAttrs
     * @dataProvider providerLoadAttrs
     * @param string $content
     * @param array $attrs
     * @param string $remain
     */
    public function testLoadAttrs($content, $attrs, $remain)
    {
        $this->assertEquals($attrs, TagParser::loadAttrs($content));
        $this->assertSame($remain, $content);
    }

    /**
     * @return array
     */
    public function providerLoadAttrs()
    {
        return [
            [
                '',
                [],
                '',
            ],
            [
                "  \n  s",
                [],
                '  s',
            ],
            [
                'qwei9jwerj',
                [],
                'qwei9jwerj',
            ],
            [
                ":one:two:th ree:four\nfive",
                ['one', 'two', 'th'],
                "ree:four\nfive",
            ],
            [
                ":php\n  a=1;\n",
                ['php'],
                "  a=1;\n",
            ],
            [
                ':',
                [''],
                '',
            ],
        ];
    }

    /**
     * @covers ::loadNextComponent
     * @dataProvider providerLoadNextComponent
     * @param string $content
     * @param string $component
     * @param string $remain
     */
    public function testLoadNextComponent($content, $component, $remain)
    {
        $this->assertSame($component, TagParser::loadNextComponent($content));
        $this->assertSame($remain, $content);
    }

    /**
     * @return array
     */
    public function providerLoadNextComponent()
    {
        return [
            [
                '',
                '',
                '',
            ],
            [
                '   ',
                '',
                '',
            ],
            [
                'one two three',
                'one',
                'two three',
            ],
            [
                '"one two" three',
                'one two',
                'three',
            ],
            [
                '\' one " two \'three',
                ' one " two ',
                'three',
            ],
            [
                '"one two"    ',
                'one two',
                '',
            ],
            [
                '"" content eol',
                '',
                'content eol',
            ],
        ];
    }

    /**
     * @covers ::loadNextComponent
     * @dataProvider providerLoadLastComponent
     * @param string $content
     * @param string $component
     */
    public function testLoadLastComponent($content, $component)
    {
        $this->assertSame($component, TagParser::loadLastComponent($content));
    }

    /**
     * @return array
     */
    public function providerLoadLastComponent()
    {
        return [
            [
                '',
                '',
            ],
            [
                '   ',
                '',
            ],
            [
                '  one two three',
                'one two three',
            ],
            [
                ' "qwe rty"',
                'qwe rty',
            ],
            [
                ' "qwe rty"  ',
                'qwe rty',
            ],
            [
                ' "qwe rty"  z',
                '"qwe rty"  z',
            ],
            [
                '""',
                '',
            ],
        ];
    }
}
