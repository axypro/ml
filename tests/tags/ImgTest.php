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
        $handler = function ($params) {
            if ($params->src === 'tohtml') {
                $params->html = '<b>!!!</b>';
                $params->plain = '!!!';
                return;
            }
            if (\substr($params->src, 0, 1) === ':') {
                $params->src = '/i/'.\substr($params->src, 1);
                $params->css = 'i';
            }
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
                '<img src="/i/q.png" alt="" class="i" />',
                '',
            ],
            [
                ' :q.png Alt',
                ['handler' => $handler],
                '<img src="/i/q.png" alt="Alt" class="i" />',
                'Alt',
            ],
            [
                ' /i/a.png',
                ['css' => 'class'],
                '<img src="/i/a.png" alt="" class="class" />',
                '',
            ],
            [
                ' :q.png Alt',
                ['handler' => $handler, 'css' => 'class'],
                '<img src="/i/q.png" alt="Alt" class="i" />',
                'Alt',
            ],
            [
                ' tohtml Alt',
                ['handler' => $handler, 'css' => 'class'],
                '<b>!!!</b>',
                '!!!',
            ],
        ];
    }
}
