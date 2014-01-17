<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Url;

/**
 * @coversDefaultClass axy\ml\tags\Url
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerUrl
     * @param string $content
     * @param array $options
     * @param string $html
     * @param string $plain
     */
    public function testUrl($content, $options, $html, $plain)
    {
        $tag = new Url('url', $content, $options);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerUrl()
    {
        $handler = function ($url) {
            if (empty($url)) {
                return '/';
            }
            if ($url[0] === ':') {
                return 'http://mysite.loc/'.\substr($url, 1);
            }
            return $url;
        };
        return [
            [
                ' http://example.com/?x=1',
                null,
                '<a href="http://example.com/?x=1">http://example.com/?x=1</a>',
                'http://example.com/?x=1',
            ],
            [
                ' http://example.com Link <caption>',
                null,
                '<a href="http://example.com">Link &lt;caption&gt;</a>',
                'http://example.com Link <caption>',
            ],
            [
                ' "http://example.com Link" <caption>',
                null,
                '<a href="http://example.com Link">&lt;caption&gt;</a>',
                'http://example.com Link <caption>',
            ],
            [
                '',
                null,
                '',
                '',
            ],
            [
                '',
                ['handler' => $handler],
                '<a href="/">/</a>',
                '/',
            ],
            [
                ' http://yandex.ru Я',
                ['handler' => $handler],
                '<a href="http://yandex.ru">Я</a>',
                'http://yandex.ru Я',
            ],
            [
                ' :folder/page',
                ['handler' => $handler],
                '<a href="http://mysite.loc/folder/page">http://mysite.loc/folder/page</a>',
                'http://mysite.loc/folder/page',
            ],
            [
                ' :folder/page Link',
                ['handler' => $handler],
                '<a href="http://mysite.loc/folder/page">Link</a>',
                'http://mysite.loc/folder/page Link',
            ],
            [
                ' http://yandex.ru Я',
                ['css_class' => 'link'],
                '<a href="http://yandex.ru" class="link">Я</a>',
                'http://yandex.ru Я',
            ],
        ];
    }
}
