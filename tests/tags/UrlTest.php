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
        $handler = function ($params) {
            $url = $params->url;
            if ($url === '!!!') {
                $params->html = '<b>!!!</b>';
                $params->plain = '...';
            }
            if (empty($url)) {
                $params->url = '/';
                $params->caption = '/';
            } elseif ($url[0] === ':') {
                $params->url = 'http://mysite.loc/'.\substr($url, 1);
                $params->caption = $params->caption.'!';
                $params->css = 'internal';
            }
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
                '<a href="http://mysite.loc/folder/page" class="internal">:folder/page!</a>',
                'http://mysite.loc/folder/page',
            ],
            [
                ' :folder/page Link',
                ['handler' => $handler],
                '<a href="http://mysite.loc/folder/page" class="internal">Link!</a>',
                'http://mysite.loc/folder/page Link',
            ],
            [
                ' http://yandex.ru Я',
                ['css' => 'link'],
                '<a href="http://yandex.ru" class="link">Я</a>',
                'http://yandex.ru Я',
            ],
            [
                ' :page Link',
                ['css' => 'link', 'handler' => $handler],
                '<a href="http://mysite.loc/page" class="internal">Link!</a>',
                'http://mysite.loc/page Link',
            ],
            [
                ' !!! Link',
                ['css' => 'link', 'handler' => $handler],
                '<b>!!!</b>',
                '!!! ...',
            ],
        ];
    }
}
