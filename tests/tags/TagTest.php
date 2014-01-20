<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\Tag;

/**
 * @coversDefaultClass axy\ml\tags\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTag
     * @param string $content
     * @param string $html
     */
    public function testTag($content, $html)
    {
        $tag = new Tag('tag', $content);
        $this->assertSame($html, $tag->getHTML());
    }

    /**
     * @return array
     */
    public function providerTag()
    {
        return [
            [
                ':B',
                '<b>',
            ],
            [
                ':Div:a2  style="display:none"',
                '<div style="display:none">',
            ],
            [
                'attr',
                '',
            ],
            [
                ':0',
                '<0>',
            ],
            [
                ':0 a="v"',
                '<0 a="v">',
            ],
        ];
    }
}
