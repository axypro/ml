<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\tags;

use axy\ml\tags\ClosingTag;

/**
 * @coversDefaultClass axy\ml\tags\ClosingTag
 */
class ClosingTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerClosingTag
     * @param string $content
     * @param string $html
     */
    public function testClosingTag($content, $html)
    {
        $tag = new ClosingTag('/', $content);
        $this->assertSame($html, $tag->getHTML());
    }

    /**
     * @return array
     */
    public function providerClosingTag()
    {
        return [
            [
                'B',
                '</b>',
            ],
            [
                'B   ',
                '</b>',
            ],
            [
                '',
                '',
            ],
            [
                'div   qwre wer ewr',
                '</div qwre wer ewr>',
            ],
            [
                ' Ss Qq',
                '</ss Qq>',
            ],
            [
                '"Ss Qq" Ww',
                '</ss qq Ww>',
            ],
        ];
    }
}
