<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\TagsList;

/**
 * @coversDefaultClass axy\ml\TagsList
 */
class TagsListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $custom = [
        'br' => null,
        'url' => [
            'options' => [
                'handler' => ['axy\ml\tests\nstst\tags\One', 'handleUrl', [':']],
            ],
        ],
        'one' => '\axy\ml\tests\nstst\tags\One',
        'unk' => '\axy\ml\tests\nstst\tags\Unk',
        'c' => '=code',
        'cc' => [
            'classname' => '=c',
            'options' => ['attr_lang' => 'lang'],
        ],
        'ccc' => '=unknown',
        'php' => ['=cc', ['lang' => 'php']],
        'div' => 'HtmlTag',
        'ftp' => ['Scheme', []],
        'line' => [
            'classname' => 'HtmlTag',
            'options' => ['single' => true],
            'name' => 'hr',
        ],
        '0' => [
            'classname' => 'Scheme',
        ],
        '1' => [
            'classname' => 'Scheme',
            'name' => '0',
        ],
    ];

    /**
     * @covers ::getParams
     * @dataProvider providerGetParams
     * @param string $name
     * @param string $expected
     */
    public function testGetParams($name, $expected)
    {
        $list = new TagsList($this->custom);
        $this->assertEquals($expected, $list->getParams($name));
    }

    /**
     * @return array
     */
    public function providerGetParams()
    {
        return [
            [
                'unknown',
                null,
            ],
            [
                'br',
                null,
            ],
            [
                'b',
                [
                    'classname' => 'axy\ml\tags\HtmlTag',
                    'options' => [],
                    'name' => 'b',
                ],
            ],
            [
                'url',
                [
                    'classname' => 'axy\ml\tags\Url',
                    'options' => [
                        'handler' => ['axy\ml\tests\nstst\tags\One', 'handleUrl', [':']],
                    ],
                    'name' => 'url',
                ],
            ],
            [
                'one',
                [
                    'classname' => 'axy\ml\tests\nstst\tags\One',
                    'options' => [],
                    'name' => 'one',
                ],
            ],
            [
                'unk',
                null,
            ],
            [
                'c',
                [
                    'classname' => 'axy\ml\tags\Code',
                    'options' => [],
                    'name' => 'code',
                ],
            ],
            [
                'cc',
                [
                    'classname' => 'axy\ml\tags\Code',
                    'options' => ['attr_lang' => 'lang'],
                    'name' => 'code',
                ],
            ],
            [
                'ccc',
                null,
            ],
            [
                'php',
                [
                    'classname' => 'axy\ml\tags\Code',
                    'options' => ['attr_lang' => 'lang', 'lang' => 'php'],
                    'name' => 'code',
                ],
            ],
            [
                'div',
                [
                    'classname' => 'axy\ml\tags\HtmlTag',
                    'options' => [],
                    'name' => 'div',
                ],
            ],
            [
                'ftp',
                [
                    'classname' => 'axy\ml\tags\Scheme',
                    'options' => [],
                    'name' => 'ftp',
                ],
            ],
            [
                '',
                [
                    'classname' => 'axy\ml\tags\Html',
                    'options' => [],
                    'name' => '',
                ],
            ],
            [
                '/',
                [
                    'classname' => 'axy\ml\tags\ClosingTag',
                    'options' => [],
                    'name' => '/',
                ],
            ],
            [
                'line',
                [
                    'classname' => 'axy\ml\tags\HtmlTag',
                    'options' => ['single' => true],
                    'name' => 'hr',
                ],
            ],
            [
                '0',
                [
                    'classname' => 'axy\ml\tags\Scheme',
                    'options' => [],
                    'name' => '0',
                ],
            ],
            [
                '1',
                [
                    'classname' => 'axy\ml\tags\Scheme',
                    'options' => [],
                    'name' => '0',
                ],
            ],
        ];
    }

    /**
     * @covers ::create
     * @dataProvider providerCreate
     * @param string $name
     * @param string $content
     * @param string $html
     */
    public function testCreate($name, $content, $html)
    {
        $list = new TagsList($this->custom);
        $tag = $list->create($name, $content);
        if ($html !== null) {
            $this->assertInstanceOf('axy\ml\tags\Base', $tag);
            $this->assertSame($html, $tag->getHTML());
        } else {
            $this->assertNull($tag);
        }
    }

    /**
     * @return array
     */
    public function providerCreate()
    {
        return [
            [
                'br',
                '',
                null,
            ],
            [
                'unk',
                '',
                null,
            ],
            [
                'ccc',
                '',
                null,
            ],
            [
                'unknown',
                '',
                null,
            ],
            [
                'url',
                ' :page Link',
                '<a href="http://example.loc/page">Link</a>',
            ],
            [
                'one',
                ':a:b xx "yy zz" aa',
                'a.b:xx.yy zz.aa',
            ],
            [
                'code',
                ':js x=1;',
                '<code rel="js">x=1;</code>',
            ],
            [
                'c',
                ':js x=1;',
                '<code rel="js">x=1;</code>',
            ],
            [
                'cc',
                ':js x=1;',
                '<code lang="js">x=1;</code>',
            ],
            [
                'php',
                ':js x=1;',
                '<code lang="php">x=1;</code>',
            ],
            [
                'div',
                ' rel="1"',
                '<div rel="1">',
            ],
            [
                'ftp',
                '://ftp.server.loc/',
                '<a href="ftp://ftp.server.loc/">ftp://ftp.server.loc/</a>',
            ],
            [
                'line',
                '',
                '<hr />',
            ],
        ];
    }
}
