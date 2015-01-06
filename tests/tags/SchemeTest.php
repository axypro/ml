<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Scheme;
use axy\ml\tests\nstst\Factory;

/**
 * coversDefaultClass axy\ml\tags\Scheme
 */
class SchemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerScheme
     * @param string $content
     * @param string $html
     * @param string $plain
     */
    public function testScheme($name, $content, $html, $plain)
    {
        $handler = function ($params) {
            if ($params->url === 'http://e') {
                $params->url = 'e';
            }
        };
        $tags = [
            'url' => [
                'options' => [
                    'css' => 'urlclass',
                    'handler' => $handler,
                ],
            ],
            'ftp' => [
                'classname' => 'Scheme',
                'options' => [
                    'use_url' => false,
                    'css' => 'ftpclass',
                ],
            ],
        ];
        $context = Factory::createContext(null, $tags);
        $tag = $context->tags->create($name, $content, $context);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerScheme()
    {
        return [
            [
                'http',
                '://yandex.ru/',
                '<a href="http://yandex.ru/" class="urlclass">http://yandex.ru/</a>',
                'http://yandex.ru/',
            ],
            [
                'https',
                '://yandex.ru',
                '<a href="https://yandex.ru" class="urlclass">https://yandex.ru</a>',
                'https://yandex.ru',
            ],
            [
                'http',
                '://yandex.ru this is <alt>',
                '<a href="http://yandex.ru" class="urlclass">this is &lt;alt&gt;</a>',
                'http://yandex.ru this is <alt>',
            ],
            [
                'http',
                '://e this is <alt>',
                '<a href="e" class="urlclass">this is &lt;alt&gt;</a>',
                'http://e this is <alt>',
            ],
            [
                'ftp',
                '://server.loc this is alt',
                '<a href="ftp://server.loc" class="ftpclass">ftp://server.loc</a>',
                'ftp://server.loc',
            ],
            [
                'ftp',
                '"://server.loc this is alt"',
                '<a href="ftp://server.loc this is alt" class="ftpclass">ftp://server.loc this is alt</a>',
                'ftp://server.loc this is alt',
            ],
        ];
    }
}
