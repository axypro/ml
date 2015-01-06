<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Html;
use axy\ml\tests\nstst\Factory;

/**
 * coversDefaultClass axy\ml\tags\Html
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
        $context = Factory::createContext();
        $tag = new Html('code', $content, [], $context);
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
