<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Html;

/**
 * @coversDefaultClass axy\ml\tags\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHtml
     * @param string $content
     * @param string $html
     */
    public function testHtml($content, $html, $plain)
    {
        $tag = new Html('html', $content);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerHtml()
    {
        return [
            [
                ' <b>b</b> ',
                '<b>b</b>',
                'b',
            ],
            [
                ':attr:two <div>text</div> ',
                ':attr:two <div>text</div>',
                ':attr:two text',
            ],
        ];
    }
}
