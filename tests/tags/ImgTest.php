<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Img;

/**
 * @coversDefaultClass axy\ml\tags\Img
 */
class ImgTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerImg
     * @param string $content
     * @param array $options
     * @param string $html
     * @param string $plain
     */
    public function testImg($content, $options, $html, $plain)
    {
        $tag = new Img('img', $content, $options);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerImg()
    {
        $handler = function ($src) {
            if (empty($src)) {
                return null;
            }
            if ($src[0] === ':') {
                return '/i/'.\substr($src, 1);
            }
            return $src;
        };
        return [
            [
                ' /i/a.png',
                null,
                '<img src="/i/a.png" alt="" />',
                '',
            ],
            [
                ' /i/a.png Alt <text>',
                null,
                '<img src="/i/a.png" alt="Alt &lt;text&gt;" />',
                'Alt <text>',
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
                '',
                '',
            ],
            [
                ' /i/a.png Alt',
                ['handler' => $handler],
                '<img src="/i/a.png" alt="Alt" />',
                'Alt',
            ],
            [
                ' :q.png',
                ['handler' => $handler],
                '<img src="/i/q.png" alt="" />',
                '',
            ],
            [
                ' :q.png Alt',
                ['handler' => $handler],
                '<img src="/i/q.png" alt="Alt" />',
                'Alt',
            ],
        ];
    }
}
