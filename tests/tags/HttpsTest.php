<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Https;

/**
 * @coversDefaultClass axy\ml\tags\Https
 */
class HttpsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerRender
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testRender($content, $html, $plain)
    {
        $tag = new Https('https', $content);
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
                '<a href="https://example.com/?x=1">https://example.com/?x=1</a>',
                'https://example.com/?x=1',
            ],
            [
                '://example.com Link <caption>',
                '<a href="https://example.com">Link &lt;caption&gt;</a>',
                'https://example.com Link <caption>',
            ],
            [
                '"://example.com Link" <caption>',
                '<a href="https://example.com Link">&lt;caption&gt;</a>',
                'https://example.com Link <caption>',
            ],
            [
                '',
                '',
                '',
            ],
        ];
    }
}
