<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\helpers;

use axy\ml\helpers\Highlight;
use axy\ml\Options;
use axy\ml\helpers\Token;

/**
 * coversDefaultClass axy\ml\helpers\Highlight
 */
class HighlightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::text
     * @dataProvider providerText
     * @param string $text
     * @param array $options
     * @param string $expected
     */
    public function testText($text, $options, $expected)
    {
        $options = $this->createOptions($options);
        $this->assertSame($expected, Highlight::text($text, $options));
    }

    /**
     * @return array
     */
    public function providerText()
    {
        $formatter = function ($params) {
            $params->content = str_replace('-', '-->', $params->content);
        };
        $formatter2 = function ($params) {
            $params->content .= '!';
            $params->escape = !$params->escape;
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
            [
                'Con&ent',
                ['textHandler' => $formatter2],
                'Con&ent!',
            ],
            [
                'Con&ent',
                ['textHandler' => $formatter2, 'escape' => false],
                'Con&amp;ent!',
            ],
        ];
    }

    /**
     * covers ::header
     * @dataProvider providerHeader
     * @param array $params
     * @param array $options
     * @param string $expected
     */
    public function testHeader($params, $options, $expected)
    {
        $token = new Token(Token::TYPE_HEADER, 0);
        foreach ($params as $k => $v) {
            $token->$k = $v;
        }
        $options = $this->createOptions($options);
        $this->assertSame($expected, Highlight::header($token, $options));
    }

    /**
     * @return array
     */
    public function providerHeader()
    {
        $handler = function ($token) {
            return '!'.$token->content.'!';
        };
        return [
            [
                ['content' => 'Top header', 'level' => 1],
                [],
                '<h1>Top header</h1>',
            ],
            [
                ['content' => 'This is header', 'level' => 4],
                [],
                '<h4>This is header</h4>',
            ],
            [
                ['content' => 'This is header', 'level' => 8],
                [],
                '<h6>This is header</h6>',
            ],
            [
                ['content' => 'This is header', 'level' => 1],
                ['hStart' => 3],
                '<h3>This is header</h3>',
            ],
            [
                ['content' => 'This is header', 'level' => 4],
                ['hStart' => 3],
                '<h6>This is header</h6>',
            ],
            [
                ['content' => 'This is > header', 'level' => 3],
                ['hHandler' => $handler],
                '!This is > header!',
            ],
            [
                ['content' => 'This is > header', 'level' => 3],
                [],
                '<h3>This is &gt; header</h3>',
            ],
            [
                ['content' => 'This is > header', 'level' => 3],
                ['escape' => false],
                '<h3>This is > header</h3>',
            ],
            [
                ['content' => 'This is > header', 'level' => 3, 'link' => ''],
                ['escape' => false],
                '<h3>This is > header</h3>',
            ],
            [
                ['content' => 'This is > header', 'level' => 3, 'link' => 'hname'],
                ['escape' => false],
                '<h3 id="hname">This is > header</h3>',
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
