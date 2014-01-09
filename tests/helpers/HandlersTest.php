<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\helpers;

use axy\ml\helpers\Handlers;
use axy\ml\Options;

/**
 * @coversDefaultClass axy\ml\helpers\Handlers
 */
class HandlersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers escapeText
     * @dataProvider providerText
     * @param string $text
     * @param array $options
     * @param string $expected
     */
    public function testText($text, $options, $expected)
    {
        $options = $this->createOptions($options);
        $this->assertSame($expected, Handlers::text($text, $options));
    }

    /**
     * @return array
     */
    public function providerText()
    {
        $formatter = function ($text) {
            return \str_replace('-', '-->', $text);
        };
        return [
            [
                'this is <b>text</b> &copy; "me"',
                [],
                'this is &lt;b&gt;text&lt;/b&gt; &amp;copy; &quot;me&quot;',
            ],
            [
                "no\nbr",
                [],
                "no\nbr",
            ],
            [
                'this is <b>text</b> &copy; "me"',
                ['escape' => false],
                'this is <b>text</b> &copy; "me"',
            ],
            [
                '<b>One</b> - <b>two</b>',
                ['escape' => false, 'textHandler' => $formatter],
                '<b>One</b> --> <b>two</b>',
            ],
            [
                '<b>One</b> - <b>two</b>',
                ['textHandler' => $formatter],
                '&lt;b&gt;One&lt;/b&gt; --&gt; &lt;b&gt;two&lt;/b&gt;',
            ],
        ];
    }

    /**
     * @param array $options
     * @return \axy\ml\Options
     */
    private function createOptions($options)
    {
        return new Options($options);
    }
}
