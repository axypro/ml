<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Url;
use axy\ml\Result;
use axy\ml\Options;
use axy\ml\TagsList;
use axy\ml\Context;
use axy\ml\helpers\Tokenizer;

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
                '',
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
                ':folder/page',
            ],
            [
                ' :folder/page Link',
                ['handler' => $handler],
                '<a href="http://mysite.loc/folder/page" class="internal">Link!</a>',
                ':folder/page Link',
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
                ':page Link',
            ],
            [
                ' !!! Link',
                ['css' => 'link', 'handler' => $handler],
                '<b>!!!</b>',
                '...',
            ],
        ];
    }

    /**
     * @dataProvider providerInlineImg
     * @param array $customTags
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testInlineImg($customTags, $content, $html, $plain)
    {
        $tokenizer = new Tokenizer('');
        $options = new Options();
        $tags = new TagsList($customTags);
        $result = new Result($tokenizer, $options, $tags);
        $context = new Context($result, $options, $tags, null);
        $tag = $tags->create('url', $content, $context);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerInlineImg()
    {
        return [
            [
                [],
                ' link a.png <alt>',
                '<a href="link">a.png &lt;alt&gt;</a>',
                'link a.png <alt>',
            ],
            [
                [],
                ':plain link a.png <alt>',
                '<a href="link">a.png &lt;alt&gt;</a>',
                'link a.png <alt>',
            ],
            [
                [],
                ':unk link a.png <alt>',
                '<a href="link">a.png &lt;alt&gt;</a>',
                'link a.png <alt>',
            ],
            [
                [],
                ':HTML link a.png <alt>',
                '<a href="link">a.png <alt></a>',
                'link a.png <alt>',
            ],
            [
                [],
                ':IMG link a.png <alt>',
                '<a href="link"><img src="a.png" alt="&lt;alt&gt;" /></a>',
                'link <alt>',
            ],
            [
                [],
                ':IMG link a.png',
                '<a href="link"><img src="a.png" alt="" /></a>',
                'link',
            ],
            [
                [],
                ':IMG "a b" "c d" e f',
                '<a href="a b"><img src="c d" alt="e f" /></a>',
                'a b e f',
            ],
            [
                [],
                ':IMG',
                '',
                '',
            ],
            [
                [],
                ':IMG link',
                '<a href="link">link</a>',
                'link',
            ],
            [
                [
                    'url' => [
                        'options' => [
                            'css' => 'a',
                            'css_img' => 'aimg',
                        ],
                    ],
                    'img' => [
                        'options' => [
                            'css' => 'c',
                        ],
                    ],
                ],
                ':IMG link a.png alt',
                '<a href="link" class="aimg"><img src="a.png" alt="alt" class="c" /></a>',
                'link alt',
            ],
            [
                [
                    'url' => [
                        'options' => [
                            'css' => 'a',
                            'css_img' => 'aimg',
                            'handler' => function ($params) {
                                $params->css .= 'z';
                                $params->plain = '-'.$params->plain.'-';
                            },
                        ],
                    ],
                    'img' => [
                        'options' => [
                            'css' => 'c',
                            'handler' => function ($params) {
                                $params->css .= 'x';
                                $params->plain = 'qq';
                            },
                        ],
                    ],
                ],
                ':IMG link a.png alt',
                '<a href="link" class="aimgz"><img src="a.png" alt="alt" class="cx" /></a>',
                '-link qq-',
            ],
        ];
    }
}
