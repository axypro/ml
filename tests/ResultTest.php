<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests;

use axy\ml\Parser;
use axy\ml\helpers\Token;

/**
 * @coversDefaultClass axy\ml\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $axyml = $this->getFile('base.axyml');
        $html = $this->getFile('base.html');
        $plain = $this->getFile('base.txt');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertSame($plain, rtrim($result->plain));
        $this->assertFalse($result->isCutted);
        $this->assertSame('basic, test', $result->meta->keywords);
        $this->assertSame('Title of the document', $result->title);
        $errors = [];
        foreach ($result->errors as $error) {
            $errors[] = (string)$error;
        }
        $expected = [
            'Header is empty on line 4',
            'Meta is empty on line 8',
            'Invalid [img]: empty src on line 15',
            'Unknown tag [unknown] on line 26',
            'Tag [br] is not closed on line 31',
        ];
        $this->assertEquals($expected, $errors);
        $expected = [
            [
                'content' => 'Title of the document',
                'level' => 1,
                'name' => null,
                'link' => null,
            ],
            [
                'content' => 'Subtitle',
                'level' => 2,
                'name' => 'cut',
                'link' => 'cut',
            ],
            [
                'content' => 'Header of level 3',
                'level' => 3,
                'name' => null,
                'link' => null,
            ],
            [
                'content' => '2 level > header',
                'level' => 2,
                'name' => null,
                'link' => null,
            ],
        ];
        $this->assertEquals($expected, $result->getHeaders());
        $expected = [
            [
                'content' => 'Title of the document',
                'level' => 1,
                'name' => null,
                'link' => null,
            ],
            [
                'content' => 'Subtitle',
                'level' => 2,
                'name' => 'cut',
                'link' => 'cut',
            ],
            [
                'content' => '2 level > header',
                'level' => 2,
                'name' => null,
                'link' => null,
            ],
        ];
        $this->assertEquals($expected, $result->getHeaders(2));
    }

    public function testCut()
    {
        $axyml = $this->getFile('base.axyml');
        $html1 = $this->getFile('base.html');
        $html2 = $this->getFile('cut.html');
        $parser = new Parser();
        $result1 = $parser->parse($axyml, 'unk');
        $this->assertSame($html1, rtrim($result1->html));
        $this->assertFalse($result1->isCutted);
        $result2 = $parser->parse($axyml, 'anchor');
        $this->assertSame($html2, rtrim($result2->html));
        $this->assertTrue($result2->isCutted);
    }

    /**
     * Test escape, textHandler, hStart, bTags, block_separator, code: default_lang, unknown tag
     */
    public function testMany()
    {
        $textHandler = function ($params) {
            $params->content = str_replace(2, 3, $params->content);
        };
        $options = [
            'escape' => false,
            'textHandler' => $textHandler,
            'hStart' => 2,
            'bTags' => ['<div>', '</div>'],
            'beauty' => false,
        ];
        $tags = [
            'code' => [
                'options' => [
                    'default_lang' => 'php',
                ],
            ],
            'unknown' => 'HtmlTag',
            'b' => null,
        ];
        $axyml = $this->getFile('base.axyml');
        $html = $this->getFile('many.html');
        $parser = new Parser($options, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
    }

    /**
     * Test hHandler, tag alias and block split
     */
    public function testBlocks()
    {
        $hHandler = function ($header) {
            return '<div class="h'.$header->level.'">'.$header->content.'</div>';
        };
        $options = [
            'hHandler' => $hHandler
        ];
        $tags = [
            'php' => [
                'classname' => '=code',
                'options' => [
                    'lang' => 'php',
                ],
            ],
        ];
        $axyml = $this->getFile('blocks.axyml');
        $html = $this->getFile('blocks.html');
        $parser = new Parser($options, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Test bHandler, tag alias & tag name
     */
    public function testHand()
    {
        $bHandler = function ($text) {
            return '<span>'.str_replace("\n", '', $text).'</span>';
        };
        $options = [
            'bHandler' => $bHandler
        ];
        $tags = [
            'hr' => [
                'classname' => 'HtmlTag',
                'options' => ['single' => true],
            ],
            'aliashr' => '=hr',
            'line' => [
                'classname' => 'HtmlTag',
                'options' => ['single' => true],
                'name' => 'hr',
            ],
        ];
        $axyml = $this->getFile('hand.axyml');
        $html = $this->getFile('hand.html');
        $parser = new Parser($options, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * "0" is empty string
     */
    public function testEmpty0()
    {
        $axyml = $this->getFile('empty0.axyml');
        $html = $this->getFile('empty0.html');
        $plain = $this->getFile('empty0.txt');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertSame($plain, $result->plain);
        $this->assertEmpty($result->errors);
    }

    /**
     * Test new line
     */
    public function testNL()
    {
        $axyml = $this->getFile('nl.axyml');
        $html = $this->getFile('nl.html');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Test lists and CSS
     */
    public function testListsCSS()
    {
        $axyml = $this->getFile('lists-css.axyml');
        $html = $this->getFile('lists-css.html');
        $tags = [
            '*' => [
                'options' => [
                    'css' => 'cl',
                    'css_li' => 'cli',
                ],
            ],
        ];
        $parser = new Parser(null, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Test lists
     */
    public function testLists()
    {
        $axyml = $this->getFile('lists.axyml');
        $html = $this->getFile('lists.html');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Test custom context and getArg()-method
     */
    public function testCustom()
    {
        $axyml = $this->getFile('custom.axyml');
        $html = $this->getFile('custom.html');
        $tags = [
            'cus' => '\axy\ml\tests\nstst\tags\Cus',
        ];
        $parser = new Parser(null, $tags, ['value' => 2]);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Test [URL:IMG]
     */
    public function testUrlImg()
    {
        $axyml = $this->getFile('urlimg.axyml');
        $html = $this->getFile('urlimg.html');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertCount(1, $result->errors);
    }

    /**
     * Test css option for Scheme
     */
    public function testSchemeCSS()
    {
        $axyml = $this->getFile('scheme-css.axyml');
        $html = $this->getFile('scheme-css.html');
        $tags = [
            'http' => [
                'options' => [
                    'use_url' => false,
                    'css' => 'cc',
                ],
            ],
            'ftp' => [
                'classname' => 'Scheme',
                'options' => [
                    'use_url' => false,
                ],
            ],
            'al' => '=http',
            'al2' => [
                'classname' => '=http',
                'options' => [
                    'css' => 'al2',
                ],
            ],
        ];
        $parser = new Parser(null, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertEmpty($result->errors);
    }

    /**
     * Check options for empty("0")
     */
    public function testOptionsEmpty0()
    {
        $axyml = $this->getFile('empty0-options.axyml');
        $html = $this->getFile('empty0-options.html');
        $tags = [
            'code' => [
                'options' => [
                    'css_block' => '0',
                    'css_inline' => '0',
                    'default_lang' => '0',
                    'attr_lang' => '0',
                ],
            ],
            'img' => [
                'options' => [
                    'css' => '0',
                ],
            ],
            'http' => [
                'options' => [
                    'css' => '0',
                ],
            ],
            'url' => [
                'options' => [
                    'css' => '0',
                    'css_img' => '0',
                ],
            ],
        ];
        $parser = new Parser(null, $tags);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
        $this->assertCount(1, $result->errors);
    }

    /**
     * Bug \n
     */
    public function testTable()
    {
        $axyml = $this->getFile('table.axyml');
        $html = $this->getFile('table.html');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
    }

    /**
     * Test [OPT]
     */
    public function testOpt()
    {
        $axyml = $this->getFile('opt.axyml');
        $html = $this->getFile('opt.html');
        $parser = new Parser();
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
    }

    /**
     * Test [OPT] and bHandler
     */
    public function testOptHandler()
    {
        $bHandler = function ($content, $options) {
            if (!empty($options['unk'])) {
                return '<p class="unk">U: '.$content.'</p>';
            }
            return $content;
        };
        $options = [
            'bHandler' => $bHandler,
        ];
        $axyml = $this->getFile('opt.axyml');
        $html = $this->getFile('opt-handler.html');
        $parser = new Parser($options);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
    }

    public function testHeaders()
    {
        $axyml = $this->getFile('headers.axyml');
        $html = $this->getFile('headers.html');
        $options = [
            'hStart' => 2,
            'hLinkPrefix' => 'p-',
            'hLinkNeed' => true,
        ];
        $parser = new Parser($options);
        $result = $parser->parse($axyml);
        $this->assertSame($html, rtrim($result->html));
    }

    /**
     * @covers ::replaceTokens
     */
    public function testReplaceTokens()
    {
        $options = [
            'hStart' => 2,
        ];
        $parser = new Parser($options);
        $content = "# Title\n#= x: y\nContent\n";
        $result = $parser->parse($content);
        $tokens = $result->tokens;
        $ntoken = new Token(Token::TYPE_HTML, 4);
        $ntoken->content = '<b>1</b>';
        $tokens[] = $ntoken;
        $ntoken2 = new Token(Token::TYPE_HTML, 3);
        $ntoken2->content = '<hr />';
        $tokens[1]->subs[] = $ntoken2;
        $result->replaceTokens($tokens);
        $this->assertEquals($tokens, $result->tokens);
        $expected = "<h2>Title</h2>\n\n<p>Content<hr /></p>\n\n<b>1</b>";
        $this->assertSame($expected, $result->html);
    }

    /**
     * @param string $fn
     * @return string
     */
    private function getFile($fn)
    {
        if (!isset($this->files[$fn])) {
            $filename = __DIR__.'/nstst/parse/'.$fn;
            $this->files[$fn] = rtrim(file_get_contents($filename));
        }
        return $this->files[$fn];
    }

    /**
     * @var array
     */
    private $files = [];
}
