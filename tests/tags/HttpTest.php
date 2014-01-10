<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Http;

/**
 * @coversDefaultClass axy\ml\tags\Http
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerRender
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testRender($content, $html, $plain)
    {
        $tag = new Http($content);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerRender()
    {
        return [
            [
                '://example.com/?x=1',
                '<a href="http://example.com/?x=1">http://example.com/?x=1</a>',
                'http://example.com/?x=1',
            ],
            [
                '://example.com Link <caption>',
                '<a href="http://example.com">Link &lt;caption&gt;</a>',
                'http://example.com Link <caption>',
            ],
            [
                '"://example.com Link" <caption>',
                '<a href="http://example.com Link">&lt;caption&gt;</a>',
                'http://example.com Link <caption>',
            ],
            [
                '',
                '',
                '',
            ],
        ];
    }
}
