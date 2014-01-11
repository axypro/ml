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
        'div' => 'HtmlTag',
        'ftp' => 'Scheme',
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
        ];
    }
}
