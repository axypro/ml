<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\HtmlTag;

/**
 * @coversDefaultClass axy\ml\tags\HtmlTag
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHtmlTag
     * @param string $name
     * @param string $content
     * @param string $html
     */
    public function testHtmlTag($name, $content, $options, $html)
    {
        $tag = new HtmlTag($name, $content, $options);
        $this->assertSame($html, $tag->getHTML());
    }

    /**
     * @return array
     */
    public function providerHtmlTag()
    {
        return [
            [
                'b',
                '',
                [],
                '<b>',
            ],
            [
                'div',
                'class="x" id="y"',
                [],
                '<div class="x" id="y">',
            ],
            [
                'br',
                ' /',
                [],
                '<br />',
            ],
            [
                'img',
                'src="src"',
                ['single' => true],
                '<img src="src" />',
            ],
        ];
    }
}
