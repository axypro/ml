<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Scheme;

/**
 * @coversDefaultClass axy\ml\tags\Scheme
 */
class SchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHttp
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testHttp($content, $html, $plain)
    {
        $tag = new Scheme('http', $content);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerHttp()
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

    /**
     * @dataProvider providerHttps
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testHttps($content, $html, $plain)
    {
        $tag = new Scheme('https', $content);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerHttps()
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
