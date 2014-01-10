<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tests\nstst\tags\One;
use axy\ml\tests\nstst\tags\Two;

/**
 * @coversDefaultClass axy\ml\tags\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getNextComponent
     * @covers ::getLastComponent
     * @dataProvider providerOne
     * @param string $content
     * @param array $options
     * @param string $html
     * @param array $errors
     */
    public function testOne($content, $options, $html, $errors = [])
    {
        $tag = new One($content, $options);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($html, $tag->getPlain());
        $this->assertEquals($errors, $tag->getErrors());
    }

    /**
     * @return array
     */
    public function providerOne()
    {
        return [
            [
                '',
                [],
                ':..',
                ['not enough data'],
            ],
            [
                ':one:two three four five',
                null,
                'one.two:three.four.five',
            ],
            [
                'three "four five" six seven',
                null,
                ':three.four five.six seven',
            ],
            [
                ':qwe',
                null,
                'qwe:..',
                ['not enough data'],
            ],
        ];
    }

    /**
     * @covers ::$options
     * @covers ::getPlain
     * @covers ::escape
     * @dataProvider providerTwo
     * @param string $content
     * @param array $options
     * @param string $html
     * @param array $errors
     */
    public function testTwo($content, $options, $html, $plain)
    {
        $tag = new Two($content, $options);
        $this->assertSame($html, $tag->getHTML());
        $this->assertSame($plain, $tag->getPlain());
    }

    /**
     * @return array
     */
    public function providerTwo()
    {
        return [
            [
                '',
                null,
                '3:!',
                '3:!',
            ],
            [
                '<b>',
                null,
                '3:&lt;b&gt;!',
                '3:<b>!',
            ],
            [
                ':a <b>',
                ['x' => 4],
                '6::a &lt;b&gt;!',
                '6::a <b>!',
            ],
        ];
    }
}
