<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Util;
use axy\ml\Parser;

/**
 * coversDefaultClass axy\ml\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::extractHead
     */
    public function testExtractHeadTitleOnly()
    {
        $options = [
            'content' => file_get_contents(__DIR__.'/nstst/util/head.ml'),
            'meta' => false,
            'parser' => false,
        ];
        $result = Util::extractHead($options);
        $this->assertSame('This is title', $result->title);
        $this->assertNull($result->meta);
    }

    /**
     * covers ::extractHead
     */
    public function testExtractHeadMeta()
    {
        $options = [
            'content' => file_get_contents(__DIR__.'/nstst/util/head.ml'),
            'parser' => false,
        ];
        $result = Util::extractHead($options);
        $this->assertSame('This is title', $result->title);
        $expected = [
            'one' => '1',
            'two' => true,
        ];
        $this->assertEquals($expected, $result->meta->getSource());
    }

    /**
     * covers ::extractHead
     */
    public function testExtractHeadParser()
    {
        $options1 = [
            'content' => file_get_contents(__DIR__.'/nstst/util/head-parser.ml'),
            'parser' => false,
        ];
        $result1 = Util::extractHead($options1);
        $this->assertNull($result1->title);
        $this->assertEmpty($result1->meta->getSource());
        $options2 = [
            'content' => file_get_contents(__DIR__.'/nstst/util/head-parser.ml'),
        ];
        $result2 = Util::extractHead($options2);
        $this->assertSame('This is title', $result2->title);
        $expected = [
            'one' => '1',
            'two' => true,
            'three' => '3',
        ];
        $this->assertEquals($expected, $result2->meta->getSource());
    }

    /**
     * covers ::extractHead
     */
    public function testExtractHeadFile()
    {
        $options = [
            'filename' => __DIR__.'/nstst/util/head.ml',
            'parser' => false,
        ];
        $result = Util::extractHead($options);
        $this->assertSame('This is title', $result->title);
        $expected = [
            'one' => '1',
            'two' => true,
        ];
        $this->assertEquals($expected, $result->meta->getSource());
    }

    /**
     * covers ::extractHead
     */
    public function testExtractHeadParserFile()
    {
        $options1 = [
            'filename' => __DIR__.'/nstst/util/head-parser.ml',
            'parser' => false,
            'fullload' => true,
        ];
        $result1 = Util::extractHead($options1);
        $this->assertNull($result1->title);
        $this->assertEmpty($result1->meta->getSource());
        $options2 = [
            'filename' => __DIR__.'/nstst/util/head-parser.ml',
        ];
        $result2 = Util::extractHead($options2);
        $this->assertSame('This is title', $result2->title);
        $expected = [
            'one' => '1',
            'two' => true,
            'three' => '3',
        ];
        $this->assertEquals($expected, $result2->meta->getSource());
    }

    /**
     * covers ::createMenu
     */
    public function testCreateMenu()
    {
        $content = file_get_contents(__DIR__.'/nstst/util/menu.ml');
        $parser = new Parser();
        $result = $parser->parse($content);
        $expected = [
            [
                'title' => 'Two',
                'link' => 't',
                'level' => 2,
                'subs' => [
                    [
                        'title' => 'Three',
                        'link' => null,
                        'level' => 3,
                        'subs' => [],
                    ],
                    [
                        'title' => 'Four',
                        'link' => null,
                        'level' => 3,
                        'subs' => [],
                    ],
                ],
            ],
            [
                'title' => 'Five',
                'link' => 'f',
                'level' => 2,
                'subs' => [
                    [
                        'title' => null,
                        'link' => null,
                        'level' => 3,
                        'subs' => [
                            [
                                'title' => 'Six',
                                'link' => null,
                                'level' => 4,
                                'subs' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Eight',
                'link' => 'e',
                'level' => 2,
                'subs' => [],
            ],
        ];
        $this->assertEquals($expected, Util::createMenu($result, 2, 5));
    }

    /**
     * covers ::renderMenu
     */
    public function testRenderMenu()
    {
        $content = file_get_contents(__DIR__.'/nstst/util/menu.ml');
        $parser = new Parser();
        $result = $parser->parse($content);
        $expected = [
            '<ol>',
            '<li><a href="#t">Two</a>',
            '<ol>',
            '<li>Three</li>',
            '<li>Four</li>',
            '</ol>',
            '</li>',
            '<li><a href="#f">Five</a>',
            '<ol>',
            '<li>',
            '<ol>',
            '<li>Six</li>',
            '</ol>',
            '</li>',
            '</ol>',
            '</li>',
            '<li><a href="#e">Eight</a></li>',
            '</ol>',
        ];
        $expected = implode(\PHP_EOL, $expected);
        $this->assertEquals($expected, Util::renderMenu($result, \PHP_EOL, 2, 5));
        $this->assertEquals($expected, Util::renderMenu($result->getHeaders(), \PHP_EOL, 2, 5));
        $menu = Util::createMenu($result, 2, 5);
        $this->assertEquals($expected, Util::renderMenu($menu, \PHP_EOL, 2, 5));
    }

    /**
     * covers ::mergeCustomTagsList
     */
    public function testMergeCustomTagsList()
    {
        $a = [
            'a' => 'cname',
            'c' => null,
            'd' => ['=dd'],
            'e' => ['c', ['x' => 1, 'y' => 2], 'ee'],
            'f' => 'ff',
        ];
        $b = [
            'b' => ['cn', ['x' => 1]],
            'c' => ['cc'],
            'e' => ['zz', ['y' => 3, 'z' => 4]],
            'f' => null,
        ];
        $expected = [
            'a' => 'cname',
            'b' => ['cn', ['x' => 1]],
            'c' => ['cc'],
            'd' => ['=dd'],
            'e' => [
                'classname' => 'zz',
                'options' => ['x' => 1, 'y' => 3, 'z' => 4],
                'name' => 'ee',
            ],
            'f' => null,
        ];
        $this->assertEquals($expected, Util::mergeCustomTagsList($a, $b));
    }

    /**
     * covers ::insertHTMLAfterTitle
     */
    public function testInsertHTMLAfterTitle()
    {
        $parser = new Parser();
        $content = "# This is title\n#= x:y\n## h2\ntext";
        $result = $parser->parse($content);
        $html = '<div>!</div>';
        Util::insertHTMLAfterTitle($result, $html);
        $expected = "<h1>This is title</h1>\n\n<div>!</div>\n\n<h2>h2</h2>\n\n<p>text</p>";
        $this->assertSame($expected, $result->html);
    }
}
